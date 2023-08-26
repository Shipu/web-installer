<?php

namespace Shipu\WebInstaller\Utilities;

class RequirementsChecker
{
    /**
     * Minimum PHP Version Supported (Override is in installer.php config file).
     */
    private string $minPhpVersion = '8.0.0';

    /**
     * Check for the server requirements.
     *
     * @param  array  $allRequirements
     *
     * @return array
     */
    public function check(array $allRequirements): array
    {
        $results = [];
        $mapMethod = [
            'php'    => 'checkPhpExtensions',
            'apache' => 'checkApacheModules',
        ];

        foreach ($allRequirements as $type => $requirements) {
            if ($method = $mapMethod[$type] ?? null) {
                $results['requirements'][$type]
                    = $this->{$method}($requirements);
            }
        }

        return $results;
    }

    public function checkPhpExtensions(array $requirements): array
    {
        $results = [];
        foreach ($requirements as $requirement) {
            array_set($results, $requirement,
                extension_loaded($requirement));
        }

        return $results;
    }

    public function checkApacheModules(array $requirements): array
    {
        $results = [];
        foreach ($requirements as $requirement) {
            array_set($results, $requirement,
                function_exists('apache_get_modules')
                && in_array($requirement, apache_get_modules()));
        }

        return $results;
    }

    /**
     * Check PHP version requirement.
     *
     * @return array
     */
    public function checkPhpVersion(string $minPhpVersion = null): array
    {
        $currentPhpVersion = $this->getPhpVersionInfo();
        $minVersionPhp = $minPhpVersion ?? $this->getMinPhpVersion();
        $supported = version_compare($currentPhpVersion['version'],
                $minVersionPhp) >= 0;

        return [
            'full'      => $currentPhpVersion['full'],
            'current'   => $currentPhpVersion['version'],
            'minimum'   => $minVersionPhp,
            'supported' => $supported,
        ];
    }

    /**
     * Get current Php version information.
     *
     * @return array
     */
    private static function getPhpVersionInfo(): array
    {
        $currentVersionFull = PHP_VERSION;
        preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
        $currentVersion = $filtered[0];

        return [
            'full'    => $currentVersionFull,
            'version' => $currentVersion,
        ];
    }

    /**
     * Get minimum PHP version.
     *
     * @return string minPhpVersion
     */
    protected function getMinPhpVersion(): string
    {
        return $this->minPhpVersion;
    }
}