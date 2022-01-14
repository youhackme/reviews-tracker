<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'review_id',
        'notification_type',
    ];
}