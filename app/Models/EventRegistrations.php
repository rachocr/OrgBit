<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistrations extends Model
{
    use HasFactory;

    protected $table = 'event_registrations'; // Define table name
    protected $primaryKey = 'registration_id'; // Set primary key
    public $timestamps = false; // Disable timestamps if not needed

    protected $fillable = [
        'event_id',
        'member_id',
        'status',
        'joined_at',
        'qr_code',
    ];

    // Relationship: Get the member
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    // Relationship: Get the event
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'event_id');
    }
}