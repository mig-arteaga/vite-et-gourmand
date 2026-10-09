<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database {
    public static function connect(): PDO {
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
                $password
            );

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $pdo;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}