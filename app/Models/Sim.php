<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sim extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sim_number',
        'camera_name',
        'camera_location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
