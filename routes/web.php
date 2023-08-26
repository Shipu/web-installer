<?php

use Illuminate\Support\Facades\Route;
use Shipu\WebInstaller\Livewire\Installer;

Route::get('installer', Installer::class)->name('installer')
    ->middleware(['web']);