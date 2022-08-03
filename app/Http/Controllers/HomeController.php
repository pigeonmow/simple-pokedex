<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Shows the application home page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('pages.home.index');
    }
}
