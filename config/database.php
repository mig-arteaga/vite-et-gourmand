<?php

if (getenv("DATABASE_URL")) {

    // Production (Railway)
    $database = parse_url(getenv("DATABASE_URL"));

    $host = $database["host"];
    $dbname = ltrim($database["path"], "/");
    $username = $database["user"];
    $password = $database["pass"];

} else {

    // Local (XAMPP)
    $host = "localhost";
    $dbname = "vite_et_gourmand";
    $username = "root";
    $password = "";

}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
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