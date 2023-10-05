<?php

namespace Shipu\WebInstaller\Forms\Fields;

use Filament\Forms\Components\Wizard\Step;
use Shipu\WebInstaller\Concerns\StepContract;
use Shipu\WebInstaller\Forms\Components\ViewBorder;
use Shipu\WebInstaller\Utilities\PermissionsChecker;

class FolderPermissionStep implements StepContract
{
    public static function form(): array
    {
        $fields = [];
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
        }

        return $fields;
    }

    public static function make(): Step
    {
        return Step::make('permissions')
            ->label('Permissions')
            ->schema(self::form());
    }
}
