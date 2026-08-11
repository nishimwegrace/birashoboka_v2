<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->create('volets', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name');
        $table->string('slogan')->nullable();
        $table->string('subtitle')->nullable();
        $table->text('description')->nullable();
        $table->enum('target', ['young', 'women', 'all']);
        $table->string('place');
        $table->timestamps();
    });
};
