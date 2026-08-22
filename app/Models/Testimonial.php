<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';
    protected $fillable = ['activity_id', 'name', 'role', 'photo', 'content', 'rating'];
    public $timestamps = true;

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
