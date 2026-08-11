<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->create('partners', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name');
        $table->unsignedBigInteger('volet_id')->nullable();
        $table->timestamps();

        $table->index('volet_id');
        $table->foreign('volet_id')->references('id')->on('volets')->onDelete('set null');
    });
};
