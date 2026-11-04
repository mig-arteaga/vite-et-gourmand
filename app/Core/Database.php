<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function connect(): PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        if (getenv('MYSQLHOST')) {
            $host = getenv('MYSQLHOST');
            $dbname = getenv('MYSQLDATABASE');
            $username = getenv('MYSQLUSER');
            $password = getenv('MYSQLPASSWORD');
            $port = getenv('MYSQLPORT');
        } else {
            $host = 'localhost';
            $dbname = 'vite_et_gourmand';
            $username = 'root';
            $password = '';
            $port = 3306;
        }

        try {
            $pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            self::$instance = $pdo;

            return self::$instance;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
