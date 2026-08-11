<?php

namespace App\Controllers;

class HealthController extends Controller
{
    public static function index(): void
    {
        apiResponse(true, 'OK', ['time' => date('c')]);
    }
}
