<?php

use Illuminate\Support\Facades\Route;
use Shipu\WebInstaller\Livewire\Installer;

Route::get('/', function () {
    return view('installer::success');
})->name('installer.success');

Route::get('installer', Installer::class)->name('installer')
    ->middleware(['web']);