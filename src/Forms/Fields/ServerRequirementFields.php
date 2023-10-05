<?php

namespace Shipu\WebInstaller\Forms\Fields;

use Filament\Forms\Components\Wizard\Step;
use Shipu\WebInstaller\Forms\Components\ViewBorder;
use Shipu\WebInstaller\Utilities\RequirementsChecker;

class ServerRequirementFields
{
    public static function form(): array
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
            $value = $requirements['requirements']['php'][$extensions] ?? false;
            $fields[] = ViewBorder::make('server_requirements.'
                .strtolower($extensions).'_view')
                ->label(studly_case($extensions))
                ->required(! ($value === true))
                ->inlineLabel()
                ->default($value);
        }

        return $fields;
    }

    public static function make(): Step
    {
        return Step::make('server')
            ->label('Server Requirements')
            ->schema(self::form());
    }
}
