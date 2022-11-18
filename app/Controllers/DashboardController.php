<?php

namespace App\Controllers;

use Inertia\Inertia;

class DashboardController extends BaseController
{
    public function index()
    {
        return Inertia::render('Dashboard/Index');
    }
}
