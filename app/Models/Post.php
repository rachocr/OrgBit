<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    // Define the table name explicitly
    protected $table = 'post'; // Ensure this matches your actual table name in the database
    public $timestamps = false;

    // Define which fields are mass assignable
    protected $fillable = [
        'post_content',
        'event_id',
        'media_id',
        'org_id',
        'post_date_time',
        'post_title',
    ];

    // Define the primary key if it's not 'id'
    protected $primaryKey = 'post_id';

    // Treat post_date_time as a Carbon instance
    protected $casts = [
        'post_date_time' => 'datetime', // Use $casts for better flexibility
    ];

    // Define relationships with other models
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'event_id');
    }

    public function media()
    {
        return $this->belongsTo(MediaContent::class, 'media_id', 'media_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }
}