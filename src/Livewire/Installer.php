<?php

namespace Shipu\WebInstaller\Livewire;

use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class Installer extends Component implements HasForms
{
    use InteractsWithForms;

    public array $data = [];

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount()
    {
        $this->setDefaultValues();

        if (file_exists(storage_path('installed'))) {
            try {
                return redirect(config('installer.redirect_route'));
            } catch (\Exception $exception) {
                Log::info("route not found...");
                Log::info($exception->getMessage());
                return redirect()->route('installer.success');
            }
        }
    }

    public function setDefaultValues(): void
    {
        $default = [];
        foreach (config('installer.environment.form') as $envKey => $config) {
            array_set($default, 'environments.'.$envKey,
                config($config['config_key']));
        }

        $this->form->fill($default);
    }

    public function getSteps(): array
    {
        $stepConfigs = config('installer.steps', []);
        $steps = [];
        foreach ($stepConfigs as $class) {
            $steps[] = $class::make();
        }

        return $steps;
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make($this->getSteps())
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        type="submit"
                        wire:loading.attr="disabled"
                        size="sm"
                        icon="heroicon-m-sparkles"
                    >
                        Install
                        <span wire:loading>ongoing. Please Wait...</span>
                    </x-filament::button>
                BLADE
                )))
        ];
    }

    public function save(): Redirector|Application|RedirectResponse
    {
        $inputs = $this->form->getState();

        $installationManager = app(config('installer.installation_manager'));
        $result = $installationManager->run($inputs);

        Notification::make()
            ->title($result ? 'Successfully Installed' : 'Installation Failed')
            ->success()
            ->send();

        return $installationManager->redirect();
    }

    public function dehydrate(): void
    {
        Log::info("installation dehydrate...");
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }

    public function render()
    {
        return view('web-installer::livewire.installer')
            ->layout('web-installer::components.layouts.app');
    }
}
