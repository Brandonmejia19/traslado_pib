<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Vormkracht10\TwoFactorAuth\Enums\TwoFactorType;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
class User extends Authenticatable implements FilamentUser,LdapAuthenticatable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable,AuthenticatesWithLdap, HasRoles,HasSuperAdmin;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Implement your logic to determine if the user can access the panel
        return true;
    }
    public function getLdapDomainColumn(): string
    {
        return 'domain';
    }

    public function getLdapGuidColumn(): string
    {
        return 'guid';
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
