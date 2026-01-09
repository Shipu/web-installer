<?php

namespace Shipu\WebInstaller;

use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Shipu\WebInstaller\Livewire\Installer;
use Shipu\WebInstaller\Middleware\RedirectIfNotInstalled;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WebInstallerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        Livewire::component('web-installer', Installer::class);

        $this->app['router']->aliasMiddleware('redirect.if.not.installed', RedirectIfNotInstalled::class);

        $package->name('web-installer')
            ->hasAssets()
            ->hasViews('web-installer')
            ->hasConfigFile('installer')
            ->hasRoute('web');
    }

    public function packageBooted(): void
    {
        $this->app['config']['session.driver'] = 'file';
        $this->app['config']['cache.default'] = 'file';
        if (file_exists(storage_path('installed')) && Schema::hasTable('sessions') && Schema::hasTable('cache')) {
            $this->app['config']['session.driver'] = 'database';
            $this->app['config']['cache.default'] = 'database';
        }
    }
}
