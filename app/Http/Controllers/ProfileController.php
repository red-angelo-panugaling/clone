<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('User.profile');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')->ignore($user)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=3000,max_height=3000'],
        ]);

        if ($validator->fails()) {
            return redirect()->to(route('profile.show').'#profile-edit')
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        $validated = $validator->validated();
        $previousProfilePicturePath = $user->profile_picture_path;

        $user->name = $validated['full_name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile-pictures/'.$user->id, 'public');

            if ($profilePicturePath === false) {
                return redirect()->to(route('profile.show').'#profile-edit')
                    ->withErrors(['profile_picture' => 'The profile picture could not be uploaded. Please try again.'])
                    ->withInput($request->except(['password', 'password_confirmation', 'profile_picture']));
            }

            $user->profile_picture_path = $profilePicturePath;
        }

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        if ($request->hasFile('profile_picture') && filled($previousProfilePicturePath)) {
            Storage::disk('public')->delete($previousProfilePicturePath);
        }

        return redirect()->to(route('profile.show').'#profile')->with('success', 'Your profile has been updated.');
    }
}
