<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendance extends Model
{
    use HasFactory;

    protected $table = 'event_attendance'; // Define table name
    protected $primaryKey = 'attendance_id'; // Set primary key
    public $timestamps = false; // Disable timestamps if not needed

    protected $fillable = [
        'event_id', // Add this
        'member_id', // Add this
        'qr_code', // Add this
        'status', // Add this
        'joined_at', // Add this if needed
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    // Define the relationship to the Event model
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id', 'event_id');
    }
}