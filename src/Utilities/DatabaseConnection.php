<?php

namespace Shipu\WebInstaller\Utilities;

use Exception;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\DB;

class DatabaseConnection
{
    public function check($environmentUpdatedInfo): array
    {
        $databaseConfig
            = $this->makeNewConfig($environmentUpdatedInfo['database']);

        DB::purge();

        try {
            app(ConnectionFactory::class)->make($databaseConfig)->getPdo();

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

    public function makeNewConfig($databaseConnectionInfo): array
    {
        $connection = $databaseConnectionInfo['driver'] ?? 'mysql';
        $connectionConfig = config('database.connections.'.$connection);

        $connectionConfig['host'] = $databaseConnectionInfo['host'];
        $connectionConfig['port'] = $databaseConnectionInfo['port'];
        $connectionConfig['database'] = $databaseConnectionInfo['name'];
        $connectionConfig['username'] = $databaseConnectionInfo['username'];
        $connectionConfig['password'] = $databaseConnectionInfo['password'];

        return $connectionConfig;
    }
}