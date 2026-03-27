<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'hotline'
    ];

    // Quan hệ: 1 Cơ sở có nhiều Vé/Game (nhiều nhiều)
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }

    
}