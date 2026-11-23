<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('search')->toString());
        $role = $request->input('role');
        $status = $request->input('status');
        $perPage = in_array($request->input('per_page'), ['10', '15', '25', '50'], true)
            ? (int) $request->input('per_page')
            : 15;

        $query = User::withoutLinkedEmployees()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($role, [User::ROLE_ADMIN, User::ROLE_USER], true), function ($query) use ($role): void {
                if (Schema::hasColumn('users', 'role')) {
                    $query->whereRaw('LOWER(TRIM(role)) = ?', [strtolower($role)]);

                    return;
                }

                if (Schema::hasColumn('users', 'role_id') && Schema::hasTable('roles')) {
                    $administratorRoleIds = DB::table('roles')
                        ->select('id')
                        ->whereRaw('LOWER(TRIM(slug)) = ?', ['administrator']);

                    if ($role === User::ROLE_ADMIN) {
                        $query->whereIn('users.role_id', $administratorRoleIds);

                        return;
                    }

                    $query->whereNotIn('users.role_id', $administratorRoleIds);
                }
            })
            ->when($status === 'active', fn ($query) => $query->where('users.status', true))
            ->when($status === 'inactive', fn ($query) => $query->where('users.status', false))
            ->when(! in_array($status, ['active', 'inactive', 'all'], true), fn ($query) => $query->where('users.status', true));

        if (Schema::hasColumn('users', 'role_id') && Schema::hasTable('roles')) {
            $query->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->addSelect('users.*')
                ->selectRaw("CASE WHEN LOWER(TRIM(roles.slug)) = 'administrator' THEN 'Admin' ELSE 'User' END as role");
        }

        return view('Admin.users.index', [
            'users' => $query->orderBy('users.id')->paginate($perPage)->withQueryString(),
            'totalUsers' => User::withoutLinkedEmployees()->count(),
            'activeUsers' => User::withoutLinkedEmployees()->where('status', true)->count(),
            'inactiveUsers' => User::withoutLinkedEmployees()->where('status', false)->count(),
        ]);
    }

    public function show(User $user): View
    {
        $role = $user->role;

        if (Schema::hasColumn('users', 'role_id') && Schema::hasTable('roles')) {
            $role = DB::table('roles')->where('id', $user->role_id)->value('name') ?? $role;
        }

        return view('Admin.users.show', [
            'user' => $user,
            'role' => $role,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        User::create([
            'name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'status' => $validated['status'] === 'active',
        ]);

        return back()->with('success', 'User account created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')->ignore($user)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
        ]);

        if ($user->is(auth()->user()) && $validated['role'] !== User::ROLE_ADMIN) {
            return back()->with('danger', 'You cannot remove your own administrator role.');
        }

        if ($user->isAdmin() && $user->status && $validated['role'] !== User::ROLE_ADMIN && $this->activeAdministratorCount() === 1) {
            return back()->with('danger', 'You cannot remove the role from the last administrator account.');
        }

        $user->name = $validated['full_name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        return back()->with('success', 'User account updated successfully.');
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($user->is(auth()->user()) && $validated['status'] === 'inactive') {
            return back()->with('danger', 'You cannot deactivate your own account.');
        }

        $user->status = $validated['status'] === 'active';
        $user->save();

        return back()->with('success', 'User account status updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->with('danger', 'You cannot delete your own account.');
        }

        if ($user->isAdmin() && $user->status && $this->activeAdministratorCount() === 1) {
            return back()->with('danger', 'You cannot delete the last administrator account.');
        }

        $user->status = false;
        $user->save();

        return back()->with('success', 'User account removed from the active system. Set its database status to 1 to restore it.');
    }

    private function activeAdministratorCount(): int
    {
        if (Schema::hasColumn('users', 'role')) {
            return User::query()
                ->whereRaw('LOWER(TRIM(role)) = ?', [strtolower(User::ROLE_ADMIN)])
                ->where('status', true)
                ->count();
        }

        if (Schema::hasColumn('users', 'role_id') && Schema::hasTable('roles')) {
            return User::query()
                ->whereIn('role_id', DB::table('roles')->select('id')->whereRaw('LOWER(TRIM(slug)) = ?', ['administrator']))
                ->where('status', true)
                ->count();
        }

        return 0;
    }
}
