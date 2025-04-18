<?php

include_once "../include/_include.php";

if (is_logged_in()) {
    header("location: form.php");
    exit;
}

if (isset($_POST['email'], $_POST['password'])) {
    $post = $_POST;

    $post['email'] = trim($post['email']);

    $query = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param("s", $post['email']);
    $query->execute();
    $user = $query->get_result()->fetch_assoc();

    if (!$user) {
        $_SESSION['notification'] = [
            "message" => "E-mail incorrecte.",
            "type" => "danger",
        ];
        header("location: login.php");
        exit;
    };

    if (!password_verify($post['password'], $user['password'])) {
        $_SESSION['notification'] = [
            "message" => "Mot de passe incorrecte.",
            "type" => "danger",
        ];
        header("location: login.php");
        exit;
    }

    login($user);

    header("location: form.php");
    exit;
}

include "../include/_header.php";
include "../include/_notifs.php";

?>

<div class="container w-100 h-100 d-flex align-items-center justify-content-center">
    <div class="card col-6">
        <div class="card-header">Connexion</div>
        <div class="card-body p-4">
            <form class="" method="post">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="E-mail" aria-label="E-mail"
                        aria-describedby="email" required>
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Mot de passe" aria-label="Mot de passe"
                        aria-describedby="password" required>
                </div>

                <div class="mb-4">
                    <a href="register.php">Pas de compte ?</a>
                </div>

                <input type="submit" class="btn btn-primary rounded col-12" value="Se connecter">
            </form>
        </div>
    </div>
</div>

<?php

include "../include/_footer.php";
