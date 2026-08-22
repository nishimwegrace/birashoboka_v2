<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->table('activities', function (Blueprint $table) {
        $table->string('icon')->nullable()->after('description');
    });
};
