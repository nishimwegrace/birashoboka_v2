<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->create('posts', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('volet_id');
        $table->string('title');
        $table->text('description');
        $table->timestamp('published_at')->nullable();
        $table->timestamps();

        $table->index('volet_id');
        $table->foreign('volet_id')->references('id')->on('volets')->onDelete('cascade');
    });
};
