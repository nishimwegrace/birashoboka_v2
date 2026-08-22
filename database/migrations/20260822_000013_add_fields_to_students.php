<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return function () {
    Capsule::schema()->table('students', function (Blueprint $table) {
        $table->date('birth_date')->nullable()->after('age');
        $table->string('nationality')->nullable()->after('birth_date');
        $table->string('province')->nullable()->after('nationality');
        $table->string('commune')->nullable()->after('province');
        $table->string('vulnerability_category')->nullable()->after('address');
        $table->string('education_level')->nullable()->after('vulnerability_category');
    });
};
