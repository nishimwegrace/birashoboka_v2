<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaigns';
    protected $fillable = [
        'volet_id',
        'activity_id',
        'edition',
        'title',
        'description',
        'registration_start',
        'registration_end',
        'start_date',
        'end_date',
        'place',
    ];
    public $timestamps = true;

    public function volet()
    {
        return $this->belongsTo(Volet::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }
}
