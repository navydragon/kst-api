<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Filial;
use App\Http\Resources\FilialResource;


class FilialController extends Controller
{
    public function index()
    {
        return FilialResource::collection(Auth::user()->filials());
    }

    public function publicIndex()
    {
        return FilialResource::collection(Filial::orderBy('name')->get());
    }
}
