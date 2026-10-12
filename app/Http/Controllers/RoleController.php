<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Auth;


class RoleController extends Controller
{
    function index() {
        return RoleResource::collection(Auth::user()->roles());
    }
}
