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

    // Quan hệ: 1 Cơ sở có nhiều Vé/Game
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    
}