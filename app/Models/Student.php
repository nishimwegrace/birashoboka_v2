<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'age',
        'birth_date',
        'nationality',
        'province',
        'commune',
        'address',
        'vulnerability_category',
        'education_level',
        'interest',
    ];
    public $timestamps = true;

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }
}
