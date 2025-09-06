<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'n3tdata_plainid',
        'full_name',
        'description',
        'image',
        'color',
        'type',
        'is_active',
        'coverage_percentage',
        'service_areas',
        'contact_info',
        'sort_order',
    ];

    protected $casts = [
        'service_areas' => 'array',
        'contact_info' => 'array',
        'is_active' => 'boolean',
        'coverage_percentage' => 'decimal:2',
    ];

    /**
     * Scope for active networks
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-network.png');
    }

    /**
     * Get formatted coverage percentage
     */
    public function getCoverageDisplayAttribute()
    {
        return $this->coverage_percentage ? $this->coverage_percentage . '%' : 'N/A';
    }

    /**
     * Get service areas as string
     */
    public function getServiceAreasDisplayAttribute()
    {
        if ($this->service_areas && is_array($this->service_areas)) {
            return implode(', ', $this->service_areas);
        }
        return 'All Nigeria';
    }
}
