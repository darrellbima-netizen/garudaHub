<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    // Database hanya menggunakan created_at
    public const UPDATED_AT = null;

    // Relasi: User sebagai client memiliki banyak project
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    // Relasi: User sebagai developer memiliki banyak bid
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'developer_id');
    }
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
