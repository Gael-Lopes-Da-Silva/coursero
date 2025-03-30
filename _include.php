<?php

include_once "_define.php";

session_start();

$mysqli = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function login(array $user): void {
    $_SESSION['user'] = $user;
}

function logout(): void {
    unset($_SESSION['user']);
}
