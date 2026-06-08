<?php

namespace App\Shared\Database;

use App\Shared\Exception\DatabaseException;
use PDO;
use PDOException;

// System Path(for php)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// browser path (for css, js, images)
if (!defined('BASE_URL')) {
    define('BASE_URL', '/MediaLibrary-MVC-Test');
}

class Database
{
    private static ?PDO $connection = null;
    private static string $host = '127.0.0.1';
    private static string $port = '3306';
    private static string $dbname = 'Database01';
    private static string $user = 'root';
    private static string $pass = '';

    private static function loadFromEnv(): void
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $name = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        if ($host !== false && $host !== '') {
            self::$host = $host;
        }

        if ($port !== false && $port !== '') {
            self::$port = $port;
        }

        if ($name !== false && $name !== '') {
            self::$dbname = $name;
        }

        if ($user !== false && $user !== '') {
            self::$user = $user;
        }

        if ($pass !== false) {
            self::$pass = $pass;
        }
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::loadFromEnv();
            try {
                self::$connection = new PDO(
                    'mysql:host=' . self::$host . ';port=' . self::$port . ';dbname=' . self::$dbname . ';charset=utf8',
                    self::$user,
                    self::$pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $exception) {
                throw new DatabaseException(
                    'Database connection failed: ' . $exception->getMessage()
                );
            }
        }

        return self::$connection;
    }
}
