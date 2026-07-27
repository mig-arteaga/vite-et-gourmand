<?php

require_once "back-end/session.php";

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

function requireRoles($roles) {
    if (!in_array(getRole(), $roles)) {
        header("Location: index.php");
        exit;
    }
}

?>