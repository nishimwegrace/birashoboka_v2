<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->create('inscriptions', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('campaign_id');
        $table->unsignedBigInteger('student_id');
        $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
        $table->timestamps();

        $table->index('campaign_id');
        $table->index('student_id');
        $table->unique(['campaign_id', 'student_id']);

        $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
        $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
    });
};
