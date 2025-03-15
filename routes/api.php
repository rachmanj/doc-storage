<?php

use App\Http\Controllers\Api\DocumentController;
use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Protected document routes (require API key)
Route::middleware([ApiKeyMiddleware::class])->group(function () {
    Route::apiResource('documents', DocumentController::class);
});

// Public document access route
Route::get('documents/access/{token}', [DocumentController::class, 'access'])->name('documents.access'); 