<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Admin Routes
    Route::middleware(['role:masteradmin'])->group(function () {
        Route::view('admin/users', 'pages.admin.users')->name('admin.users');
        Route::view('admin/roles', 'pages.admin.roles')->name('admin.roles');
    });

    Route::middleware(['role:masteradmin|state_coordinator'])->group(function () {
        Route::view('admin/vendors', 'pages.admin.vendors')->name('admin.vendors');
    });
});

require __DIR__.'/settings.php';
