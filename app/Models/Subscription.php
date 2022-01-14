<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Subscription extends Model
{
    protected $fillable = [
        'id',
        'application_id',
        'user_id',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', '=', 2);
    }
}