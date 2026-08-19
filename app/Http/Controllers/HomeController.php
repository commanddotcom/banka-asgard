<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $banks = Bank::query()->orderBy('order')->orderBy('title')->get();

        return view('home', ['banks' => $banks]);
    }
}
