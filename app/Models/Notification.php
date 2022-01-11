<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    protected $fillable = [
        'id',
        'users_id',
        'reviews_id',
        'notification_type',
    ];
}