<?php

namespace Shipu\WebInstaller\Concerns;

use Filament\Schemas\Components\Wizard\Step;

interface StepContract
{
    public static function make(): Step;
}