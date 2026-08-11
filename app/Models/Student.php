<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $fillable = ['name', 'email', 'phone', 'gender', 'age', 'address', 'interest'];
    public $timestamps = true;

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }
}
