<?php

namespace App\Http\Controllers;

class FrontendController extends Controller
{
public function welcome()
{
// return a basic welcome message
//return view('welcome', ['message' => 'Welcome to Bazar.com application!']);
    return response()->json(['message' => 'Welcome to Bazar.com application!']);
}
}
