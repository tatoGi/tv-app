<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceCode extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid()
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }
}