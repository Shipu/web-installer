<?php

namespace Shipu\WebInstaller\Forms\Fields;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Shipu\WebInstaller\Concerns\FieldContract;
use Shipu\WebInstaller\Utilities\EnvironmentHelper;

class EnvironmentFields implements FieldContract
{
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

    public static function make(): Step
    {
        return Step::make('environment')
            ->label('Environment')
            ->schema(self::form())
            ->afterValidation(function ($state) {
                $environment = $state['environments'] ?? [];
                foreach (config('installer.environment.form') as $key => $config) {
                    $newValue = array_get($environment, $key);
                    $environmentHelper = new EnvironmentHelper();
                    $environmentHelper->putPermanentEnv($config['env_key'], $newValue);
                }
            });
    }
}