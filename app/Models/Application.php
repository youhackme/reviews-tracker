<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Review;

class Application extends Model
{

    protected $fillable = [
        'id',
        'application_id',
        'name',
        'screenshots',
        'icon',
        'developer_url',
        'languages',
        'reviews_count',
        'score',
        'url',
        'released_at',
        'developer_id',
        'genre',
    ];

    protected $casts = [
        'screenshots' => 'object',
        'languages'   => 'object',
    ];

    public function setReleasedAtAttribute($value)
    {
        $this->attributes['released_at'] = Carbon::parse($value)->setTimezone('UTC');
    }


    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

}