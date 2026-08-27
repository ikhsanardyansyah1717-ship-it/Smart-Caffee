<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CustomerController extends Controller
{
    public function home(): View { return view('customer.home'); }
    public function orders(): View { return view('customer.orders'); }
    public function favorites(): View { return view('customer.favorites'); }
    public function profile(): View { return view('customer.profile'); }
}
