<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class College extends Model
{
    use HasFactory;

    protected $table = 'college';
    // Define primary key if it's not 'id'
    protected $primaryKey = 'college_id'; 

    // If 'course_id' is not auto-incrementing (manually set), set this to false
    public $incrementing = false;

    protected $fillable = ['college_id','college_name'];
}
