<?php

require_once "session.php";

if (!isLoggedIn()) {
    header("Location: assets/login.php");
    exit;
}

function requireRoles($roles) {
    if (!in_array(getRole(), $roles)) {
        header("Location: index.php");
        exit;
    }
}

?>