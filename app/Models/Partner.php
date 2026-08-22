<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';
    protected $fillable = ['name', 'volet_id', 'logo', 'type', 'website_url'];
    public $timestamps = true;

    public function volet()
    {
        return $this->belongsTo(Volet::class);
    }
}
