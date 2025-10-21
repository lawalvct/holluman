<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class N3tdataPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'network_id',
        'plan_type',
        'plan_name',
        'amount',
        'duration',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: N3tdata plan belongs to a network
     */
    public function network()
    {
        return $this->belongsTo(Network::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '₦' . number_format($this->amount, 2);
    }

    /**
     * Scope to filter by network
     */
    public function scopeForNetwork($query, $networkId)
    {
        return $query->where('network_id', $networkId);
    }

    /**
     * Scope to filter by plan type
     */
    public function scopeByType($query, $planType)
    {
        return $query->where('plan_type', $planType);
    }
}
