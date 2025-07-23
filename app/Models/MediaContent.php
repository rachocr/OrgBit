<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaContent extends Model
{
    use HasFactory;

    protected $table = 'mediacontent'; // Correct table name
    protected $primaryKey = 'media_id';
    public $timestamps = true;

    // Fillable fields for mass assignment
    protected $fillable = [
        'file_name',
        'file_type',
        'file_url',
        'org_id', // Replaced uploaded_by with org_id
    ];

    // Add relationships if necessary
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }
}
