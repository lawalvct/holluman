<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'network_id',
        'subscriber_phone',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'start_date',
        'end_date',
        'status',
        'auto_renew',
        'plan_snapshot',
        'n3tdata_request_id',
        'n3tdata_response',
        'data_activated_at',
        'data_activation_failed_at',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'plan_snapshot' => 'array',
        'n3tdata_response' => 'array',
        'auto_renew' => 'boolean',
        'amount_paid' => 'decimal:2',
        'data_activated_at' => 'datetime',
        'data_activation_failed_at' => 'datetime',
    ];

    /**
     * User who owns this subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Subscription plan
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Network for this subscription
     */
    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    /**
     * Payments for this subscription
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->end_date);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>', now());
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountAttribute()
    {
        return '₦' . number_format($this->amount_paid, 2);
    }
}
