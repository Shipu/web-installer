<?php

use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Shipu\WebInstaller\Concerns\InstallationContract;
use Shipu\WebInstaller\Concerns\StepContract;
use Shipu\WebInstaller\Livewire\Installer;

it('renders the installer page with every step', function () {
    $this->withoutVite();

    $this->get(route('installer'))
        ->assertOk()
        ->assertSee('Server Requirements')
        ->assertSee('Permissions')
        ->assertSee('Environment')
        ->assertSee('Application Settings');
});

it('fills the environment fields with the current configuration', function () {
    config()->set('app.name', 'My App');

    Livewire::test(Installer::class)
        ->assertSet('data.environments.app.name', 'My App');
});

it('builds every configured step as a Filament wizard step', function () {
    foreach (config('installer.steps') as $step) {
        expect(is_subclass_of($step, StepContract::class))->toBeTrue()
            ->and($step::make())->toBeInstanceOf(Step::class);
    }
});

it('reports an unreachable database on the environment step', function () {
    $this->useReachableDatabase();

    Livewire::test(Installer::class)
        ->set('data.environments.database.name', '/non-existent/database.sqlite')
        ->tap(fn ($component) => submitWizardStep($component, 3))
        ->assertHasFormErrors(['environments.database.name']);
});

it('writes the environment file when the environment step is valid', function () {
    $this->useReachableDatabase();

    Livewire::test(Installer::class)
        ->set('data.environments.app.name', 'Installed')
        ->set('data.environments.database.name', ':memory:')
        ->tap(fn ($component) => submitWizardStep($component, 3))
        ->assertHasNoFormErrors()
        ->assertWizardCurrentStep(4);

    expect(file_get_contents($this->environmentDirectory.'/.env'))
        ->toContain('APP_NAME=Installed');
});

it('pre-fills the application settings with their configured defaults', function () {
    Livewire::test(Installer::class)
        ->assertSet('data.applications.admin.name', 'Mr Admin')
        ->assertSet('data.applications.admin.email', 'admin@example.com');
});

it('runs the installation manager on submit', function () {
    $this->useReachableDatabase();
    config()->set('installer.installation_manager', FakeInstallationManager::class);
    FakeInstallationManager::$data = null;

    Livewire::test(Installer::class)
        ->set('data.environments.database.name', ':memory:')
        ->set('data.applications.admin.password', 'secret')
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect('/done');

    expect(FakeInstallationManager::$data)
        ->toHaveKey('applications.admin.email', 'admin@example.com')
        ->and(password_verify('secret', FakeInstallationManager::$data['applications']['admin']['password']))->toBeTrue();
});

/**
 * Validate the given (1-based) wizard step and move to the next one.
 */
function submitWizardStep(Testable $component, int $step): void
{
    $wizard = $component->instance()->form
        ->getComponent(fn ($component): bool => $component instanceof Wizard);

    $component->call('callSchemaComponentMethod', $wizard->getKey(), 'nextStep', [$step - 1]);
}

class FakeInstallationManager implements InstallationContract
{
    public static ?array $data = null;

    public function run($data): bool
    {
        static::$data = $data;

        return true;
    }

    public function redirect(): Application|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        return redirect('/done');
    }

    public function dehydrate(): void {}
}
