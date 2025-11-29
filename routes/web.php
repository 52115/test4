<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\ClockController;
use App\Http\Controllers\StampCorrectionRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// 認証関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// メール認証
Route::get('/email/verify', [EmailVerificationController::class, 'show'])->middleware('auth')->name('verification.notice');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('auth')->name('verification.send');
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('auth')->name('verification.verify');

// 勤怠関連（認証必須）
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [ClockController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/clock-in', [ClockController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/break-start', [ClockController::class, 'breakStart'])->name('attendance.break-start');
    Route::post('/attendance/break-end', [ClockController::class, 'breakEnd'])->name('attendance.break-end');
    Route::post('/attendance/clock-out', [ClockController::class, 'clockOut'])->name('attendance.clock-out');
    
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');
    Route::post('/attendance/detail/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'list'])->name('stamp_correction_request.list')->middleware('not.admin');
});
