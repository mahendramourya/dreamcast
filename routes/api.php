<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('user',[UserController::class,'userList'])->name('user.list');
Route::post('user',[UserController::class,'create'])->name('user.create');
