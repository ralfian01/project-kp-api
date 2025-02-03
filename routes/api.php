<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\REST\V1 as RESTV1;
use App\Http\Controllers\REST\Errors;

## Base API Route
Route::post('/', [RESTV1\Home::class, 'index']);

## Authorization
Route::prefix('auth')->group(function () {
    Route::post('account', [RESTV1\Auth\Account::class, 'index']);
});

// ## Register Account
// Route::prefix('register')->group(function () {
//     Route::post('account', [RESTV1\Register\Account::class, 'index']);
// });

## My Data
Route::prefix('my')->middleware('auth:bearer')->group(function () {
    // ### Get my data
    Route::get('/', [RESTV1\My\Data::class, 'index']);

    // ### Get my privileges
    Route::get('privileges', [RESTV1\My\Privileges::class, 'index']);

    Route::prefix('application')->group(function () {
        // ### Get my applicant data
        Route::get('/', [RESTV1\My\Application\Get::class, 'index']);

        // ### Create & update draft
        Route::put('/', [RESTV1\My\Application\Draft::class, 'index']);

        // ### Propose application
        Route::put('/propose', [RESTV1\My\Application\Propose::class, 'index']);
    });
});

// ## Product
Route::prefix('product')->group(function () {
    Route::get('/', [RESTV1\Product\Get::class, 'index']);
});

## Manage
Route::prefix('manage')->middleware('auth:bearer')->group(function () {
    Route::prefix('product')->group(function () {
        Route::get('/', [RESTV1\Manage\Product\Get::class, 'index']);
        Route::get('{id}', [RESTV1\Manage\Product\Get::class, 'index']);
        Route::post('/', [RESTV1\Manage\Product\Insert::class, 'index']);
        Route::put('{id}', [RESTV1\Manage\Product\Update::class, 'index']);
        Route::delete('{id}', [RESTV1\Manage\Product\Delete::class, 'index']);
    });

    Route::prefix('machine')->group(function () {
        Route::get('/', [RESTV1\Manage\Machine\Get::class, 'index']);
        Route::get('{id}', [RESTV1\Manage\Machine\Get::class, 'index']);
        Route::post('/', [RESTV1\Manage\Machine\Insert::class, 'index']);
        Route::put('{id}', [RESTV1\Manage\Machine\Update::class, 'index']);
        Route::delete('{id}', [RESTV1\Manage\Machine\Delete::class, 'index']);
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', [RESTV1\Manage\Employee\Get::class, 'index']);
        Route::get('{id}', [RESTV1\Manage\Employee\Get::class, 'index']);
        Route::post('/', [RESTV1\Manage\Employee\Insert::class, 'index']);
        Route::put('{id}', [RESTV1\Manage\Employee\Update::class, 'index']);
        Route::delete('{id}', [RESTV1\Manage\Employee\Delete::class, 'index']);
    });

    Route::prefix('schedule')->group(function () {
        Route::get('/', [RESTV1\Manage\Schedule\Get::class, 'index']);
        Route::get('{id}', [RESTV1\Manage\Schedule\Get::class, 'index']);
        Route::post('/', [RESTV1\Manage\Schedule\Insert::class, 'index']);
        Route::put('{id}', [RESTV1\Manage\Schedule\Update::class, 'index']);
        Route::delete('{id}', [RESTV1\Manage\Schedule\Delete::class, 'index']);
    });

    Route::prefix('complain')->group(function () {
        Route::get('/', [RESTV1\Manage\Complain\Get::class, 'index']);
        Route::get('{id}', [RESTV1\Manage\Complain\Get::class, 'index']);
        Route::post('/', [RESTV1\Manage\Complain\Insert::class, 'index']);
        Route::put('{id}', [RESTV1\Manage\Complain\Update::class, 'index']);
        Route::delete('{id}', [RESTV1\Manage\Complain\Delete::class, 'index']);
    });
});

Route::prefix('report')->middleware('auth:bearer')->group(function () {
    Route::prefix('complain')->group(function () {
        Route::get('/', [RESTV1\Report\Complain\Get::class, 'index']);
        Route::get('{id}', [RESTV1\Report\Complain\Get::class, 'index']);
    });
});
