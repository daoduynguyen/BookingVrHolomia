<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Cho phép điền dữ liệu vào các cột này
    protected $fillable = [
        'category_id',
        'location_id',
        'name',
        'description',
        'image_url',
        'price',
        'price_weekend',
        'duration',
        'status',
        'avg_rating',
        'play_count',
        'trailer_url', 
        'gallery',
    ];
    
    protected $casts = [
        'gallery' => 'array',
        'ticket_types' => 'array',
    ];

    // Quan hệ: 1 Vé thuộc về 1 Danh mục
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Quan hệ: 1 Vé thuộc về 1 Cơ sở
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}