<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('consumer.dashboard');
    }
}
