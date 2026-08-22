<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->table('inscriptions', function (Blueprint $table) {
        $table->unsignedBigInteger('volet_id')->nullable()->after('student_id');
        $table->unsignedBigInteger('activity_id')->nullable()->after('volet_id');
        $table->string('reference_number')->nullable()->unique()->after('activity_id');
        $table->text('motivation')->nullable()->after('status');
        $table->text('previous_experience')->nullable()->after('motivation');
        $table->string('preferred_schedule')->nullable()->after('previous_experience');
        $table->string('preferred_center')->nullable()->after('preferred_schedule');
        $table->text('notes')->nullable()->after('preferred_center');

        $table->foreign('volet_id')->references('id')->on('volets')->onDelete('set null');
        $table->foreign('activity_id')->references('id')->on('activities')->onDelete('set null');
    });
};
