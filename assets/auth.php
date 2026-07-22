<?php

session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: assets/login.php");
    exit;

}

function requireRoles($roles) {
    if (!in_array($_SESSION['role'], $roles)) {
        header("Location: index.php");
        exit;
    }
}

?>