<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationStats extends Model
{
    use HasFactory;

    protected $table = 'organization_stats'; // Define table name

    protected $primaryKey = 'org_id'; // Set primary key

    public $timestamps = false; // Disable default timestamps

    protected $fillable = [
        'org_id', 
        'total_members', 
        'total_events', 
        'recent_posts'
    ];

    // Relationship to Organization Model
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }
}
