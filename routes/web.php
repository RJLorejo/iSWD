<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing.index')->name('landing');

require __DIR__.'/auth.php';
