<?php

namespace Shipu\WebInstaller\Forms\Fields;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Shipu\WebInstaller\Concerns\StepContract;
use Shipu\WebInstaller\Utilities\EnvironmentHelper;

class EnvironmentFields implements StepContract
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
            ->beforeValidation(function ($state) {
                $environment = $state['environments'] ?? [];
                $environmentHelper = new EnvironmentHelper();
                $environmentHelper->updateAllEnv(config('installer.environment.form'),
                    $environment);
            });
    }
}