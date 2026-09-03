<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFileController;
use App\Http\Controllers\Admin\AdminFolderController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminAuditController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserFileController;
use App\Http\Controllers\User\UserFolderController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin' 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/verify-password', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify');
Route::post('/verify-password', [ForgotPasswordController::class, 'verifyOtp']);
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset']);

Route::get('/verify-otp', [AuthController::class, 'showOtpVerify'])->name('otp.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Protected Routes ---
Route::middleware(['auth'])->group(function () {
    
    // --- Admin Space ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Gestion des Administrateurs
        Route::resource('admins', AdminManagementController::class);
        Route::post('/admins/{admin}/toggle-status', [AdminManagementController::class, 'toggleStatus'])->name('admins.toggle-status');
        
        // Gestion des Utilisateurs Standards
        Route::resource('users', UserManagementController::class);
        Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Profil Personnel Admin
        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        
        // Audit Logs
        Route::get('/audit', [AdminAuditController::class, 'index'])->name('audit.index');
        
        // Explorateur Admin (Vue globale)
        Route::get('/explorer/{folder?}', [AdminFolderController::class, 'index'])->name('explorer');
        Route::post('/folders', [AdminFolderController::class, 'store'])->name('folders.store');
        Route::put('/folders/{folder}', [AdminFolderController::class, 'update'])->name('folders.update');
        Route::delete('/folders/{folder}', [AdminFolderController::class, 'destroy'])->name('folders.destroy');
        
        // Gestion des Fichiers & Permissions
        Route::post('/files/upload', [AdminFileController::class, 'store'])->name('files.store');
        Route::get('/files/{file}', [AdminFileController::class, 'show'])->name('files.show');
        Route::get('/files/{file}/preview', [AdminFileController::class, 'preview'])->name('files.preview');
        Route::get('/files/{file}/download', [AdminFileController::class, 'download'])->name('files.download');
        Route::get('/files/{file}/permissions', [AdminFileController::class, 'permissions'])->name('files.permissions');
        Route::post('/files/{file}/permissions', [AdminFileController::class, 'grantAccess'])->name('files.permissions.grant');
        Route::delete('/files/{file}/permissions/{user}', [AdminFileController::class, 'revokeAccess'])->name('files.permissions.revoke');
        Route::patch('/files/{file}/move', [AdminFileController::class, 'move'])->name('files.move');
        Route::delete('/files/{file}', [AdminFileController::class, 'destroy'])->name('files.destroy');
    });

    // --- User Space ---
    Route::middleware(['role:user'])->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        
        // Explorateur Utilisateur (Ses fichiers + Partagés)
        Route::get('/explorer/{folder?}', [UserFolderController::class, 'index'])->name('explorer');
        Route::post('/folders', [UserFolderController::class, 'store'])->name('folders.store');
        Route::put('/folders/{folder}', [UserFolderController::class, 'update'])->name('folders.update');
        Route::delete('/folders/{folder}', [UserFolderController::class, 'destroy'])->name('folders.destroy');
        
        // Gestion de ses propres fichiers
        Route::post('/files/upload', [UserFileController::class, 'store'])->name('files.store');
        Route::patch('/files/{file}/move', [UserFileController::class, 'move'])->name('files.move');
        Route::get('/files/download/{file}', [UserFileController::class, 'download'])->name('files.download');
        Route::delete('/files/{file}', [UserFileController::class, 'destroy'])->name('files.destroy');

        // Profil Personnel Utilisateur
        Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    });

});
