<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->create('members', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name');
        $table->string('position')->nullable();
        $table->text('bio')->nullable();
        $table->string('avatar')->nullable();
        $table->string('email')->nullable();
        $table->timestamps();
    });
};
