<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->table('campaigns', function (Blueprint $table) {
        $table->boolean('is_open')->default(true)->after('place');
        $table->unsignedInteger('quota')->nullable()->after('is_open');
    });
};
