<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->table('posts', function (Blueprint $table) {
        $table->string('featured_image')->nullable()->after('description');
        $table->json('image_urls')->nullable()->after('featured_image');
    });
};
