<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Contact Management System API']);
});

Route::post("/auth/register", [AuthController::class, 'register']);
Route::post("/auth/login", [AuthController::class, 'login']);

Route::group(["middleware" => ["auth:sanctum"] ], function(){
    Route::post("/auth/logout", [AuthController::class, "logout"]);

    Route::apiResource('contacts', ContactController::class);
    Route::patch('contacts/{contact}/toggle-started', [ContactController::class, 'toggleStarted'])->name('contacts.toggle-started');
});