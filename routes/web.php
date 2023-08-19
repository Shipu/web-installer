<?php

use Illuminate\Support\Facades\Route;
use Shipu\WebInstaller\Livewire\Installer;

Route::get('shipu', function() {
    return 'Hello from the shipu package';
});

Route::get('installer', Installer::class)->name('installer')->middleware(['web']);