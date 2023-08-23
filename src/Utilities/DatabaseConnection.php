<?php

namespace Shipu\WebInstaller\Utilities;

use Exception;
use Illuminate\Support\Facades\DB;

class DatabaseConnection
{
    public function check($databaseConnectionInfo): array
    {
        $connection = 'mysql';

        $databaseConnectionInfo['database']['drive']
            = $databaseConnectionInfo['database']['driver'] ?? $connection;

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