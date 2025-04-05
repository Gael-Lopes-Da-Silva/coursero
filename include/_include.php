<?php

session_start();

$mysqli = mysqli_connect("localhost", "coursero", "coursero", "coursero");

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function login(array $user): void {
    $_SESSION['user'] = $user;
}

function logout(): void {
    unset($_SESSION['user']);
}
