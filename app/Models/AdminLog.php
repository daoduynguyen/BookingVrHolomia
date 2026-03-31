<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AdminLog extends Model
{
    protected $fillable = ['admin_id', 'action', 'model_type', 'model_id', 'details', 'ip_address'];

    protected $casts = ['details' => 'array'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Helper static method để ghi log nhanh
    public static function record(string $action, $model = null, array $details = []): void
    {
        static::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? class_basename($model) : null,
            'model_id' => $model?->id,
            'details' => $details,
            'ip_address' => Request::ip(),
        ]);
    }
}