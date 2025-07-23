<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'student_id';
    public $incrementing = false; // Since student_id is not auto-incrementing
    public $timestamps = false; // Adjust if you don’t have created_at/updated_at

    protected $fillable = [
        'student_id',
        'student_name',
        'student_email',
        'student_year',
        'course_id',
        'college_id',
    ];

    // Define the many-to-many relationship with Organization using the members table
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'members', 'student_id', 'org_id');
    }

    // Define other relationships (e.g., course, college)
    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id', 'course_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function events()
    {
        return $this->belongsToMany(Events::class, 'event_participants', 'student_id', 'event_id');
    }
    public function members()
    {
        return $this->hasMany(Member::class, 'student_id', 'student_id');
    }

}