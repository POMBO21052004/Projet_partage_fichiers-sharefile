<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'profil',
        'is_verified',
        'birthday',
        'gender',
        'code_phone',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    public function canLogin()
    {
        if ($this->status !== 'active') {
            return [
                'can_login' => false,
                'message' => 'Votre compte est inactif. Veuillez contacter l\'administrateur.'
            ];
        }

        return ['can_login' => true];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    public function sharedFiles()
    {
        return $this->belongsToMany(File::class, 'permissions')
                    ->withPivot('can_download')
                    ->withTimestamps();
    }
}
