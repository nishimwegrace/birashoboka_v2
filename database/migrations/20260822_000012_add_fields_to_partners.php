<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->table('partners', function (Blueprint $table) {
        $table->string('logo')->nullable()->after('volet_id');
        $table->string('type')->nullable()->after('logo');
        $table->string('website_url')->nullable()->after('type');
    });
};
