<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.category.categories');
    }

    public function show()
    {
        return view('admin.category.create');
    }

    public function create(Request $request)
    {
        
    }
}
