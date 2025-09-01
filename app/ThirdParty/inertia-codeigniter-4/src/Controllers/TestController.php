<?php

namespace Inertia\Controllers;

use CodeIgniter\Controller;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index()
    {
        return Inertia::render('Test', ['foo' => 'bar']);
    }
}
