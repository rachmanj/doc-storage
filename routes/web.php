<?php

use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Document viewer route (redirects to API access endpoint)
Route::get('/view/{token}', function ($token) {
    return redirect()->route('documents.access', ['token' => $token]);
})->name('documents.view');
