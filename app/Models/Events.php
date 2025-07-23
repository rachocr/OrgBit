<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    protected $table = 'events';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'event_name',
        'event_start_date',
        'event_end_date',
        'event_location',
        'event_evaluation_link',
        'event_certification_link',
        'media_id', // Add this
    ];

    protected $primaryKey = 'event_id';

    // Define a relationship to the Organization model
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }

    // Define a relationship to the Post model
    public function posts()
    {
        return $this->hasMany(Post::class, 'event_id', 'event_id');
    }
    public function media()
    {
        return $this->belongsTo(MediaContent::class, 'media_id', 'media_id');
    }

    public function registrants()
    {
        return $this->hasMany(EventRegistrations::class, 'event_id', 'event_id');
    }
}