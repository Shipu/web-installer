<?php

namespace Shipu\WebInstaller\Livewire;

use Filament\Facades\Filament;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
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
            return redirect()->intended("/");
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
                ->submitAction(new HtmlString('
                    <button 
                        wire:click="save" 
                        type="submit" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button min-h-[2rem] px-3 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700"
                    >
                        <span wire:loading.remove>Install</span>
                        <span wire:loading>Please Wait. Installing...</span>
                    </button>
            ')),
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
