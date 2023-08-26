<?php

namespace Shipu\WebInstaller\Concerns;

use Filament\Forms\Components\Wizard\Step;

interface StepContract
{
    public static function make(): Step;
}