<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PortalController extends Controller
{
    public function index(): View
    {
        return view('portal.index', ['user' => auth()->user()]);
    }
}
