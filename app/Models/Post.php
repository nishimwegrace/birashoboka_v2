<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['volet_id', 'title', 'description', 'published_at'];
    public $timestamps = true;

    public function volet()
    {
        return $this->belongsTo(Volet::class);
    }
}
