<?php

session_start();

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/.env")) {
    echo "Pas de fichier .env !";
    exit;
}

$env = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/.env");
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
