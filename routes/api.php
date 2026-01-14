<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::put('/master-items/{id}', [App\Http\Controllers\MasterItemsController::class, 'update']);
Route::post('/master-items/{id}', [App\Http\Controllers\MasterItemsController::class, 'update']); // For file uploads
Route::delete('/master-items/{id}', [App\Http\Controllers\MasterItemsController::class, 'deleteApi']);
