<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organization'; // Table name
    protected $primaryKey = 'org_id'; // Primary key column
    public $timestamps = true;

    // Define the fillable columns for mass assignment
    protected $fillable = [
        'org_email',
        'org_name',
        'org_bio',
        'org_file_path',
        'category_id',
    ];

    // Define a relationship to MediaContent
    public function mediaContents()
    {
        return $this->hasMany(MediaContent::class, 'org_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'org_id', 'org_id');
    }

    public function events()
    {
        return $this->hasMany(Events::class, 'org_id', 'org_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'members', 'org_id', 'student_id');
    }
    public function members()
    {
        return $this->hasMany(Member::class, 'org_id', 'org_id');
    }

}