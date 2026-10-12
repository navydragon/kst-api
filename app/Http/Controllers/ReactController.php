<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReactController extends Controller
{
    public function index(Request $request)
    {
        // Возвращаем содержимое index.html
        return file_get_contents(public_path('frontend/index.html'));
    }
}
