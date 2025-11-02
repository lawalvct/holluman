<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'is_active',
        'is_superadmin',
        'permissions',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_superadmin' => 'boolean',
        'permissions' => 'array',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_superadmin === true && $this->id === 1;
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Superadmin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Regular admins check their permissions array
        if ($this->isAdmin() && is_array($this->permissions)) {
            // Support two permission shapes:
            // 1) associative: [ 'dashboard' => true, 'users' => true ]
            // 2) indexed list: [ 'dashboard', 'users' ] (what the admin create form currently saves)
            // Check associative shape first, then fallback to indexed list.
            if (array_key_exists($permission, $this->permissions)) {
                return $this->permissions[$permission] === true || $this->permissions[$permission] === 1;
            }

            // Fallback: indexed list of permission keys
            return in_array($permission, $this->permissions, true);
        }

        return false;
    }

    /**
     * Check if user has any of the specified permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the specified permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all available permissions
     */
    public static function getAllPermissions(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'users' => 'Users Management',
            'sims' => 'SIMs Management',
            'plans' => 'Plans Management',
            'subscriptions' => 'Subscriptions Management',
            'payments' => 'Payments Management',
            'networks' => 'Networks Management',
            'reports' => 'Reports',
            'settings' => 'Settings',
            'admin_management' => 'Admin Management',
        ];
    }

    /**
     * User's wallet relationship
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * User's subscriptions relationship
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * User's payments relationship
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * User's wallet transactions relationship
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get user's active subscription
     */
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->latest()
            ->first();
    }

    /**
     * User's sims relationship
     */
    public function sims()
    {
        return $this->hasMany(\App\Models\Sim::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
}
