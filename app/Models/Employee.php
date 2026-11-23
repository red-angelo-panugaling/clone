<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public const STATUS_ACTIVE = true;

    public const STATUS_HIDDEN = false;

    protected $fillable = [
        'name',
        'position',
        'office',
        'age',
        'start_date',
        'salary',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'start_date' => 'date',
            'salary' => 'decimal:2',
            'status' => 'boolean',
            'employment_status' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
