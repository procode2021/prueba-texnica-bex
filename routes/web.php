<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('welcome'); // O el nombre de tu archivo Blade principal donde se monta Vue
})->where('any', '.*');
