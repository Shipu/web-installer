<?php

namespace Shipu\WebInstaller\Concerns;

use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

interface InstallationContract
{
    public function run($data): bool;

    public function redirect(): Application|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application;

    public function dehydrate(): void;
}