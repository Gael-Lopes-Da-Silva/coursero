<?php

session_start();

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function login(array $user): void {
    $_SESSION['user'] = $user;
}

function logout(): void {
    unset($_SESSION['user']);
}
