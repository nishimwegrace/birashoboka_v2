<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->create('students', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->enum('gender', ['male', 'female', 'other'])->nullable();
        $table->unsignedInteger('age')->nullable();
        $table->text('address')->nullable();
        $table->text('interest')->nullable();
        $table->timestamps();
    });
};
