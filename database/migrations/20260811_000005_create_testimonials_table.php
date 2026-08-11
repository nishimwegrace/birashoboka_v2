<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->create('testimonials', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('activity_id')->nullable();
        $table->string('name');
        $table->string('photo')->nullable();
        $table->text('content');
        $table->timestamps();

        $table->index('activity_id');
        $table->foreign('activity_id')->references('id')->on('activities')->onDelete('set null');
    });
};
