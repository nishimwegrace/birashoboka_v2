<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->table('volets', function (Blueprint $table) {
        $table->json('carousel_images')->nullable()->after('place');
    });
};
