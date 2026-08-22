<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = [
        'volet_id',
        'title',
        'description',
        'featured_image',
        'image_urls',
        'published_at',
    ];
    public $timestamps = true;

    protected $casts = [
        'image_urls' => 'array',
    ];

    public function volet()
    {
        return $this->belongsTo(Volet::class);
    }
}
