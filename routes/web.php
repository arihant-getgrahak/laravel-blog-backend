<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api');
});

Route::get('/test', function () {
    return view('test');
});
