<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ConfigurationController extends Controller
{
    public function index()
    {
        return view('admin.configurations.index');
    }
}