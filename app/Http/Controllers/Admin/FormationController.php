<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class FormationController extends Controller
{
    public function index()
    {
        return view('admin.formations.index');
    }

    public function create()
    {
        return view('admin.formations.create');
    }

    public function store()
    {
        
    }

    public function edit($id)
    {
        
    }

    public function update($id)
    {
        
    }

    public function destroy($id)
    {
        
    }

}