<?php

include_once "_include.php";

if (is_logged_in()) {
    header("location: form.php");
    exit;
} else {
    header("location: login.php");
    exit;
}
