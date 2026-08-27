<?php

namespace App\Http\Controllers;

class KitchenController extends Controller
{
    public function dashboard() { return view('kitchen.dashboard'); }
    public function incoming() { return view('kitchen.incoming'); }
    public function processing() { return view('kitchen.processing'); }
    public function completed() { return view('kitchen.completed'); }
    public function history() { return view('kitchen.history'); }
}
