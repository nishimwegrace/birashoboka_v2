<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    protected $fillable = ['volet_id', 'title', 'description'];
    public $timestamps = true;

    public function volet()
    {
        return $this->belongsTo(Volet::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }
}
