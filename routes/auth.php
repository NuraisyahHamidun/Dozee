<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ManagerAuthController;
use App\Http\Controllers\Auth\SalesmenAuthController;
use App\Http\Controllers\Auth\ManagerRegisterController;

Route::middleware('guest')->group(function () {
    // MANAGER
    Route::get('manager/login', [ManagerAuthController::class, 'create'])->name('manager.login');
    Route::post('manager/login', [ManagerAuthController::class, 'store']);
    Route::get('manager/register', [ManagerRegisterController::class, 'create'])->name('manager.register');
    Route::post('manager/register', [ManagerRegisterController::class, 'store']);

    // SALESMEN
    Route::get('salesmen/login', [SalesmenAuthController::class, 'create'])->name('salesmen.login');
    Route::post('salesmen/login', [SalesmenAuthController::class, 'store']);
});

Route::post('manager/logout', [ManagerAuthController::class, 'destroy'])->middleware('auth:manager')->name('manager.logout');
Route::post('salesmen/logout', [SalesmenAuthController::class, 'destroy'])->middleware('auth:salesmen')->name('salesmen.logout');
