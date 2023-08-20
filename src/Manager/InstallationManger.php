<?php

namespace Shipu\WebInstaller\Manager;

use Filament\Facades\Filament;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Shipu\WebInstaller\Concerns\InstallationContract;

class InstallationManger implements InstallationContract
{
    public function run($data): bool
    {
        try {
            Artisan::call('migrate:fresh');

            $user = app(config('installer.user_model'));

            $user->create([
                'name'       => array_get($data, 'applications.admin.name'),
                'email'      => array_get($data, 'applications.admin.email'),
                'password'   => array_get($data, 'applications.admin.password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Artisan::call('db:seed');

            file_put_contents(storage_path('installed'), 'installed');

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function redirect(): Application|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        if(class_exists(Filament::class)) {
            if (Filament::auth()->check()) {
                return redirect()->intended(Filament::getUrl());
            }
        }

        return redirect(config('installer.redirect_url'));
    }
}