<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LessonController;

    Route::get('/user', function (Request $request) {
    return $request->user();})->middleware('auth:sanctum');

    

    Route::post('/register', [LoginController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);

    //Роуты для авторизованных (защищённые)
    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/test', [LoginController::class, 'test']);

    Route::apiResource('lessons', LessonController::class);
    });

    //Если не авторизованы -> выводим ошибку
    Route::get('/login', function () {
    abort(403, 'Access denied');})->name('login');
