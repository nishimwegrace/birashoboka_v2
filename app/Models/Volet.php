<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volet extends Model
{
    protected $table = 'volets';
    protected $fillable = ['name', 'slogan', 'subtitle', 'description', 'target', 'place', 'carousel_images'];
    public $timestamps = true;
    protected $casts = [
        'carousel_images' => 'array',
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }
}
