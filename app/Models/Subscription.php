<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Subscription extends Model
{
    protected $fillable = [
        'id',
        'applications_id',
        'users_id',
        'status'
    ];
}