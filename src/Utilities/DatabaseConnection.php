<?php

namespace Shipu\WebInstaller\Utilities;

use Exception;
use Illuminate\Support\Facades\DB;

class DatabaseConnection
{
    public function check($databaseConnectionInfo): array
    {
        $connection = 'mysql';

        $settings = config("database.connections.$connection");
        $databaseConnectionInfo['database']['drive']
            = $databaseConnectionInfo['database']['driver'] ?? $connection;

        config([
            'database' => [
                'default'     => $connection,
                'connections' => [
                    $connection => array_merge($settings,
                        $databaseConnectionInfo['database']),
                ],
            ],
        ]);

        DB::purge();

        try {
            DB::connection()->getPdo();

            return [
                'success' => true,
                'message' => 'Database connection successful.',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}