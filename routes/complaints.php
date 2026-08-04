<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Complaint\ComplaintController;

Route::middleware(['auth'])

    ->group(function () {

        Route::resource(

            'complaints',

            ComplaintController::class

        );
    });
