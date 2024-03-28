<?php

use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::controller(ChatController::class)->group(function(){
    Route::get('/','home');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::prefix('/chat')
        ->controller(ChatController::class)
        ->name('chat.')
        ->group(function (){
            Route::prefix('/room')
                ->name('room.')
                ->group(function(){
                    Route::get('/','chatRoomIndex')->name('index');
                    Route::get('/{chatRoomId}','chatRoomShow')->name('show');
                    Route::post('/','chatRoomStore')->name('store');
                });
        });
});
