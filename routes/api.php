<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\GroupController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::apiResource('participants', ParticipantController::class);

    Route::apiResource('groups', GroupController::class); // CRUD Routes

    Route::post('/groups/{groupId}/join', [GroupController::class, 'joinGroup']); // Join group
    Route::get('/my-groups', [GroupController::class, 'userGroups']); // Get user's groups
});