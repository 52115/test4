<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StampCorrectionRequestController;
use Illuminate\Support\Facades\Route;

// 管理者ログイン（認証不要）
Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/login', [AdminLoginController::class, 'login']);
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// 管理者機能（認証必須）
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('admin.attendance.list');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('admin.attendance.show');
    Route::post('/attendance/{id}', [AttendanceController::class, 'update'])->name('admin.attendance.update');
    
    Route::get('/staff/list', [StaffController::class, 'list'])->name('admin.staff.list');
    Route::get('/attendance/staff/{id}', [StaffController::class, 'monthlyAttendance'])->name('admin.attendance.staff');
    Route::get('/attendance/staff/{id}/export', [StaffController::class, 'exportCsv'])->name('admin.attendance.staff.export');
    
    Route::get('/stamp_correction_request/list', [\App\Http\Controllers\StampCorrectionRequestController::class, 'list'])->name('admin.stamp_correction_request.list');
    Route::get('/stamp_correction_request/approve/{id}', [StampCorrectionRequestController::class, 'approve'])->name('admin.stamp_correction_request.approve');
    Route::post('/stamp_correction_request/approve/{id}', [StampCorrectionRequestController::class, 'approveRequest'])->name('admin.stamp_correction_request.approve.post');
});
