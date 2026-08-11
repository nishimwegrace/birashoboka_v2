<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->create('campaigns', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('volet_id');
        $table->unsignedBigInteger('activity_id')->nullable();
        $table->string('edition');
        $table->string('title');
        $table->text('description');
        $table->date('registration_start')->nullable();
        $table->date('registration_end')->nullable();
        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();
        $table->string('place');
        $table->timestamps();

        $table->index('volet_id');
        $table->index('activity_id');
        $table->foreign('volet_id')->references('id')->on('volets')->onDelete('cascade');
        $table->foreign('activity_id')->references('id')->on('activities')->onDelete('set null');
    });
};
