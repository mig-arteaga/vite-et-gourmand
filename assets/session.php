<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getRole() {
    return $_SESSION['role'] ?? null;
}

function isAdmin() {
    return getRole() === "Administrateur";
}

function isEmployee() {
    return in_array(getRole(), ["Administrateur", "Employé"]);
}

?>