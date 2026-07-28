<?php

if (getenv("MYSQLHOST")) {

    // Railway / Heroku
    $host = getenv("MYSQLHOST");
    $dbname = getenv("MYSQLDATABASE");
    $username = getenv("MYSQLUSER");
    $password = getenv("MYSQLPASSWORD");
    $port = getenv("MYSQLPORT");

} else {

    // Local XAMPP
    // $host = "localhost";
    $host = "127.0.0.1";
    $dbname = "vite_et_gourmand";
    $username = "root";
    $password = "";
    $port = 3306;

}

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // echo "Database connection OK";
} catch(PDOException $e) {
    echo "Database connection ERROR: " . $e->getMessage();
}

?>