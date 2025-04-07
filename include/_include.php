<?php

session_start();

$env = parse_ini_file(".env");
$mysqli = mysqli_connect($env['DB_HOST'], $env['DB_USER'], $env['DB_PASSWORD'], $env['DB_NAME']);

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function login(array $user): void {
    $_SESSION['user'] = $user;
}

function logout(): void {
    unset($_SESSION['user']);
}
