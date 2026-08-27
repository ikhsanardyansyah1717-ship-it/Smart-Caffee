<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class OwnerController extends Controller
{
    public function dashboard(): View { return view('owner.dashboard'); }
    public function sales(): View { return view('owner.sales'); }
    public function products(): View { return view('owner.products'); }
    public function employees(): View { return view('owner.employees'); }
    public function customers(): View { return view('owner.customers'); }
    public function reports(): View { return view('owner.reports'); }
}
