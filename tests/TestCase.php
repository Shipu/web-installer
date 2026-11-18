<?php

namespace Shipu\WebInstaller\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Shipu\WebInstaller\WebInstallerServiceProvider;

abstract class TestCase extends Orchestra
{
    protected string $environmentDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->environmentDirectory = sys_get_temp_dir().'/web-installer-'.uniqid();
        mkdir($this->environmentDirectory);
        file_put_contents($this->environmentDirectory.'/.env', implode("\n", [
            'APP_NAME=Laravel',
            'APP_URL=http://localhost',
            'DB_HOST=127.0.0.1',
            'DB_PORT=3306',
            'DB_DATABASE=laravel',
            'DB_USERNAME=root',
            'DB_PASSWORD=',
        ])."\n");
        $this->app->useEnvironmentPath($this->environmentDirectory);

        @unlink(storage_path('installed'));
    }

    protected function tearDown(): void
    {
        @unlink($this->environmentDirectory.'/.env');
        @rmdir($this->environmentDirectory);
        @unlink(storage_path('installed'));

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            \BladeUI\Icons\BladeIconsServiceProvider::class,
            \BladeUI\Heroicons\BladeHeroiconsServiceProvider::class,
            \Livewire\LivewireServiceProvider::class,
            \Filament\Support\SupportServiceProvider::class,
            \Filament\Actions\ActionsServiceProvider::class,
            \Filament\Schemas\SchemasServiceProvider::class,
            \Filament\Forms\FormsServiceProvider::class,
            \Filament\Infolists\InfolistsServiceProvider::class,
            \Filament\Notifications\NotificationsServiceProvider::class,
            WebInstallerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
    }

    /**
     * Point the "mysql" connection used by the installer to an in-memory
     * SQLite database, so that the database connection rule can pass.
     */
    protected function useReachableDatabase(): void
    {
        config()->set('database.connections.mysql', [
            'driver' => 'sqlite',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => ':memory:',
            'username' => 'root',
            'password' => '',
            'prefix' => '',
        ]);
    }
}
