<?php

use Illuminate\Support\Facades\Route;

/*
Route::get('/', function () {
    return view('welcome');
});
*/

// Redirect the application root to the admin panel
Route::redirect('/', '/admin');