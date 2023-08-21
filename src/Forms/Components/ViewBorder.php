<?php

namespace Shipu\WebInstaller\Forms\Components;

use Filament\Forms\Components\Field;

class ViewBorder extends Field
{
    protected string $view = 'web-installer::forms.components.view-border';

    public function getDefault()
    {
        return $this->getDefaultState();
    }

}
