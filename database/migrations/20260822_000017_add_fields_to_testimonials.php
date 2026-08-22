<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->table('testimonials', function (Blueprint $table) {
        $table->string('role')->nullable()->after('name');
        $table->tinyInteger('rating')->default(5)->after('content');
    });
};
