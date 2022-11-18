<?php

namespace App\Controllers;

use Inertia\Inertia;

class ReportsController extends BaseController
{
    public function index()
    {
        return Inertia::render('Reports/Index');
    }
}
