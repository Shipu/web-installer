<?php

namespace Shipu\WebInstaller\Concerns;

use Filament\Forms\Components\Wizard\Step;

interface FieldContract
{
    public static function make(): Step;
}