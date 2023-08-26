<?php

namespace Shipu\WebInstaller;

use Livewire\Livewire;
use Shipu\WebInstaller\Livewire\Installer;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class WebInstallerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        Livewire::component('web-installer', Installer::class);

        $package->name('web-installer')
            ->hasAssets()
            ->hasViews('web-installer')
            ->hasConfigFile('installer')
            ->hasRoute('web');
    }
}