<?php

namespace Shipu\WebInstaller\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Shipu\WebInstaller\Utilities\DatabaseConnection;

class DatabaseConnectionRule implements ValidationRule, DataAwareRule
{
    /**
     * All the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $request = $this->data['data'] ?? [];
        $environment = $request['environments'] ?? [];
        $databaseConnection = new DatabaseConnection();
        $connection = $databaseConnection->check($environment);
        if ($connection['success']) {
            return;
        }

        $fail($connection['message']);
    }
}
