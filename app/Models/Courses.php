<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Courses extends Model
{
    use HasFactory;

    // Define primary key if it's not 'id'
    protected $primaryKey = 'course_id'; 

    // If 'course_id' is not auto-incrementing (manually set), set this to false
    public $incrementing = false;

    // Define which fields are mass assignable
    protected $fillable = ['course_id', 'course_name', 'specialization'];
}
