<?php

namespace Shipu\WebInstaller\Forms\Fields;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Livewire\Features\SupportValidation\HandlesValidation;
use Shipu\WebInstaller\Concerns\FieldContract;
use Shipu\WebInstaller\Utilities\DatabaseConnection;
use Shipu\WebInstaller\Utilities\EnvironmentHelper;

class EnvironmentFields implements FieldContract
{
    use HandlesValidation;

    public static function form(): array
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

    /**
     * @throws ValidationException
     */
    public function afterEnvironmentValidation(): void
    {
        $environment = $this->data['environments'] ?? [];
        $databaseConnection = new DatabaseConnection();
        $connection = $databaseConnection->check($environment);
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
                $environmentHelper = new EnvironmentHelper();
                $environmentHelper->putPermanentEnv($config['env_key'], $newValue);
            }
        }
    }

    public static function make(): Step
    {
        return Step::make('environment')
            ->label('Environment')
            ->schema(self::form())
            ->afterValidation(function () {
                (new self())->afterEnvironmentValidation();
            });
    }
}