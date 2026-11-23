<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'full_name', 'username', 'email', 'password', 'role', 'status', 'profile_picture_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'Admin';

    public const ROLE_USER = 'User';

    public function getNameAttribute(): ?string
    {
        return $this->attributes['full_name'] ?? $this->attributes['name'] ?? null;
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
        $this->attributes['full_name'] = $value;
    }

    public function getProfilePictureUrlAttribute(): ?string
    {
        if (blank($this->profile_picture_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->profile_picture_path);
    }

    public function isAdmin(): bool
    {
        if (in_array(strtolower(trim((string) $this->role)), ['admin', 'administrator'], true)) {
            return true;
        }

        $roleId = $this->getAttribute('role_id');

        if ($roleId === null) {
            return false;
        }

        return DB::table('roles')
            ->where('id', $roleId)
            ->where(function ($query): void {
                $query->whereRaw('LOWER(TRIM(slug)) = ?', ['administrator'])
                    ->orWhereRaw('LOWER(TRIM(name)) = ?', ['administrator']);
            })
            ->exists();
    }

    public function scopeWithoutLinkedEmployees(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();

        if (Schema::hasColumn($table, 'employee_id')) {
            $query->whereNull($table.'.employee_id');
        }

        return $query;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }
}
