<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Review extends Model
{
    protected $fillable = [
        'id',
        'applications_id',
        'reviews_id',
        'version',
        'url',
        'author',
        'title',
        'description',
        'score',
        'reviewed_at',
        'votes',
    ];


    public function setReviewedAtAttribute($value)
    {
        $this->attributes['reviewed_at'] = Carbon::parse($value)->setTimezone('UTC');
    }
}