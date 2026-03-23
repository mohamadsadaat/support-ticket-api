<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ReplyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // //////////CRUD tickets////////////
    Route::apiResource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/restore', [TicketController::class, 'restore']);
    Route::delete('/tickets/{ticket}/force', [TicketController::class, 'forceDelete']);
    Route::get('/tickets/{ticket}/replies', [ReplyController::class, 'index']);
    Route::post('/tickets/{ticket}/replies', [ReplyController::class, 'store']);
});
