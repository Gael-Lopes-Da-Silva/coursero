<?php

include_once "./view/modules/_include.php";

if (isset($_GET['logout'])) logout();

if (is_logged_in()) {
    header("location: ./view/form.php");
    exit;
} else {
    header("location: ./view/login.php");
    exit;
}
