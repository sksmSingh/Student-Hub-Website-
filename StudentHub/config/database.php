
<?php

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

function db(): mysqli {
    if (!class_exists('mysqli')) {
        throw new RuntimeException(
            'The PHP mysqli extension is not enabled. Enable it in php.ini or use XAMPP.'
        );
    }

    static $connection = null;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $host = getenv('STUDENTHUB_DB_HOST') ?: '127.0.0.1';
    $user = getenv('STUDENTHUB_DB_USER') ?: 'root';
    $pass = getenv('STUDENTHUB_DB_PASS') ?: '';
    $name = getenv('STUDENTHUB_DB_NAME') ?: 'studenthub';
    $port = (int)(getenv('STUDENTHUB_DB_PORT') ?: 3306);

    try {
        $connection = new mysqli($host, $user, $pass, $name, $port);
        $connection->set_charset('utf8mb4');

        return $connection;
    } catch (mysqli_sql_exception $e) {
        error_log('StudentHub database connection failed: ' . $e->getMessage());

        throw new RuntimeException(
            'StudentHub database connection failed. Check the database name, credentials, and MySQL service.',
            0,
            $e
        );
    }
}
