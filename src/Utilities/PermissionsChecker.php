<?php

namespace Shipu\WebInstaller\Utilities;

class PermissionsChecker
{
    /**
     * Check for the folders permissions.
     *
     * @param  array  $folders
     *
     * @return array
     */
    public function check(array $folders): array
    {
        $results = [];
        foreach ($folders as $folder => $permission) {
            $results['permissions'][] = [
                'folder'     => $folder,
                'permission' => $permission,
                'isSet'      => $this->getPermission($folder) >= $permission,
            ];
        }

        return $results;
    }

    /**
     * Get a folder permission.
     *
     * @param $folder
     *
     * @return string
     */
    private function getPermission($folder): string
    {
        return substr(sprintf('%o', fileperms(base_path($folder))), -4);
    }
}