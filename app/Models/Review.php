<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Review extends Model
{
    protected $fillable = [
        'id',
        'application_id',
        'review_id',
        'version',
        'url',
        'author',
        'title',
        'description',
        'score',
        'reviewed_at',
        'votes',
        'country',
    ];


    public function setReviewedAtAttribute($value)
    {
        $this->attributes['reviewed_at'] = Carbon::parse($value)->setTimezone('UTC');
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function scopeLastHours($query, $hours)
    {
        return $query->whereRaw("`reviewed_at` >= DATE_ADD(NOW(), INTERVAL -$hours HOUR)");
    }

    public function scopeHasNotBeenSentBefore($query)
    {
        return $query->leftJoin('notifications', 'reviews.id', '=', 'notifications.review_id');
    }

}