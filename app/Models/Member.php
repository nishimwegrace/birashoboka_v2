<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';
    protected $fillable = ['name', 'position', 'bio', 'avatar', 'email'];
    public $timestamps = true;
}
