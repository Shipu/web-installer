<?php

namespace Shipu\WebInstaller\Livewire;

use App\Models\User;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Livewire\Component;
use Shipu\WebInstaller\Forms\Components\ViewBorder;
use Shipu\WebInstaller\Utilities\PermissionsChecker;
use Shipu\WebInstaller\Utilities\RequirementsChecker;

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

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Step::make('server')
                    ->label('Server Requirements')
                    ->schema($this->getServerRequirementsFields()),
                Step::make('permissions')
                    ->label('Permissions')
                    ->schema($this->getPermissionsFields()),
                Step::make('environment')
                    ->label('Environment')
                    ->schema($this->getEnvironmentFields())
                    ->afterValidation(function () {
                        $this->afterEnvironmentValidation();
                    }),
                Step::make('Application Settings')
                    ->schema($this->getApplicationFields()),
            ])
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

    public function putPermanentEnv($key, $value): void
    {
        $path = app()->environmentFilePath();

        $oldValue = env($key);
        $oldValue = preg_match('/\s/', $oldValue) ? "\"{$oldValue}\""
            : $oldValue;
        $escaped = preg_quote('='.$oldValue, '/');
        $value = preg_match('/\s/', $value) ? "\"{$value}\"" : $value;

        file_put_contents($path, preg_replace(
            "/^{$key}{$escaped}/m",
            "{$key}={$value}",
            file_get_contents($path)
        ));
    }

    /**
     * @throws ValidationException
     */
    public function afterEnvironmentValidation(): void
    {
        $environment = $this->data['environments'] ?? [];
        $connection = $this->checkDatabaseConnection($environment);
        if ( ! $connection['success']) {
            $this->withValidator(function (Validator $validator) use (
                $connection
            ) {
                $validator->after(function ($validator) use ($connection) {
                    Notification::make()
                        ->title('Database Connection Error')
                        ->body($connection['message'])
                        ->danger()
                        ->send();
                    $validator->errors()->add('environments.database.host',
                        "Database Connection Error");
                    $validator->errors()->add('environments.database.port',
                        "Database Connection Error");
                    $validator->errors()->add('environments.database.name',
                        "Database Connection Error");
                    $validator->errors()->add('environments.database.username',
                        "Database Connection Error");
                    $validator->errors()->add('environments.database.password',
                        "Database Connection Error");
                });
            })->validate();
        } elseif ($connection['success']) {
            foreach (config('installer.environment.form') as $key => $config) {
                $newValue = array_get($environment, $key);
                $this->putPermanentEnv($config['env_key'], $newValue);
            }
        }
    }

    public function checkDatabaseConnection($databaseConnectionInfo): array
    {
        $connection = 'mysql';

        $settings = config("database.connections.$connection");
        $databaseConnectionInfo['database']['drive']
            = $databaseConnectionInfo['database']['driver'] ?? $connection;

        config([
            'database' => [
                'default'     => $connection,
                'connections' => [
                    $connection => array_merge($settings,
                        $databaseConnectionInfo['database']),
                ],
            ],
        ]);

        DB::purge();

        try {
            DB::connection()->getPdo();

            return [
                'success' => true,
                'message' => 'Database connection successful.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getApplicationFields(): array
    {
        $applicationFields = [];

        foreach (config('installer.applications', []) as $key => $value) {
            if ($key == 'admin.password') {
                $applicationFields[] = TextInput::make('applications.'.$key)
                    ->label($value['label'])
                    ->password()
                    ->maxLength(255)
                    ->default($value['default'])
                    ->dehydrateStateUsing(fn($state) => ! empty($state)
                        ? Hash::make($state) : "");
            } else {
                $applicationFields[] = TextInput::make('applications.'.$key)
                    ->label($value['label'])
                    ->required($value['required'])
                    ->rules($value['rules'])
                    ->default($value['default'] ?? '');
            }
        }

        return $applicationFields;
    }

    public function getEnvironmentFields(): array
    {
        $environmentsFields = [];
        foreach (config('installer.environment.form') as $envKey => $config) {
            $environmentsFields[] = TextInput::make('environments.'.$envKey)
                ->label($config['label'])
                ->required($config['required'])
                ->rules($config['rules'])
                ->default(config($config['config_key']));
        }

        return $environmentsFields;
    }

    public function getPermissionsFields(): array
    {
        $permissionsChecker = (new PermissionsChecker());
        $filePermissions = $permissionsChecker->check(
            config('installer.permissions')
        );

        foreach ($filePermissions['permissions'] as $permission) {
            $fields[] = ViewBorder::make('permissions.'.$permission['folder']
                .'_view')
                ->label($permission['folder'])
                ->inlineLabel()
                ->required(! $permission['isSet'])
                ->default($permission['permission']);
            $fields[] = Hidden::make('permissions.'.$permission['folder'])
                ->required(function ($state) {
                    return ! ($state === true);
                })
                ->default($permission['isSet'] ? true : '');
        }

        return $fields;
    }

    public function getServerRequirementsFields(): array
    {
        $requirementChecker = (new RequirementsChecker);
        $phpSupportInfo = $requirementChecker->checkPHPversion(
            config('installer.core.minPhpVersion')
        );
        $requirements = $requirementChecker->check(
            config('installer.requirements')
        );

        $fields = [
            ViewBorder::make('server_requirements.php')
                ->inlineLabel()
                ->required(! $phpSupportInfo['supported'])
                ->default('PHP '.config('installer.core.minPhpVersion')
                    .' or higher'),

        ];
        foreach (config('installer.requirements.php') as $extensions) {
            $fields[] = ViewBorder::make('server_requirements.'
                .strtolower($extensions).'_view')
                ->label(studly_case($extensions))
                ->required(function ($state) {
                    return ! ($state === true);
                })
                ->inlineLabel()
                ->default($requirements['requirements']['php'][$extensions] ??
                    false);
//            $fields[] = Hidden::make('server_requirements.'.strtolower($extensions))
//                ->required(function ($state) {
//                    return !($state === true);
//                })
//                ->default($requirements['requirements']['php'][$extensions] ?? '');
        }

        return $fields;
    }

    public function save(): Redirector|Application|RedirectResponse
    {
        $inputs = $this->form->getState();

        Artisan::call('migrate:fresh');

        User::create([
            'name'       => array_get($inputs, 'applications.admin.name'),
            'email'      => array_get($inputs, 'applications.admin.email'),
            'password'   => array_get($inputs, 'applications.admin.password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Artisan::call('db:seed');

        Notification::make()
            ->title('Successfully Installed')
            ->success()
            ->send();

        file_put_contents(storage_path('installed'), 'installed');

        if (Filament::auth()->check()) {
            return redirect()->intended(Filament::getUrl());
        }

        return redirect(route('home'));
    }

    public function dehydrate()
    {
        Log::info("dehydrate...");
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }

    public function render()
    {
        return view('web-installer::livewire.installer')
            ->layout('web-installer::components.layouts.app');
    }
}
