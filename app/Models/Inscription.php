<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    protected $table = 'inscriptions';
    protected $fillable = [
        'campaign_id',
        'student_id',
        'volet_id',
        'activity_id',
        'reference_number',
        'status',
        'motivation',
        'previous_experience',
        'preferred_schedule',
        'preferred_center',
        'notes',
    ];
    public $timestamps = true;

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function volet()
    {
        return $this->belongsTo(Volet::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
