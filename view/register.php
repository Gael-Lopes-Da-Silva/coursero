<?php

include_once "../include/_include.php";

if (is_logged_in()) {
    header("location: form.php");
    exit;
}

if (isset($_POST['name'], $_POST['email'], $_POST['password'])) {
    $post = $_POST;

    $post['name'] = trim($post['name']);
    $post['email'] = trim($post['email']);
    $post['password'] = password_hash($post['password'], PASSWORD_DEFAULT);

    $query = $mysqli->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $query->bind_param("sss", $post['name'], $post['email'], $post['password']);

    if (!$query->execute()) {
        $_SESSION['notification'] = [
            "message" => "Problème lors de la création de l'utilisateur.",
            "type" => "danger",
        ];
        header("location: register.php");
        exit;
    };

    $_SESSION['notification'] = [
        "message" => "Votre compte a été créé, veuillez maintenant vous connecter.",
        "type" => "success",
    ];

    header("location: login.php");
    exit;
}

include "../include/_header.php";
include "../include/_notifs.php";

?>

<div class="container w-100 h-100 d-flex align-items-center justify-content-center">
    <div class="card col-6">
        <div class="card-header">Création de compte</div>
        <div class="card-body p-4">
            <form class="" method="post">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-person-fill"></i></span>
                    <input type="text" class="form-control" name="name" placeholder="Nom" aria-label="Nom"
                        aria-describedby="name" required>
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="E-mail" aria-label="E-mail"
                        aria-describedby="email" required>
                </div>

                <div class="input-group mb-4">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Mot de passe" aria-label="Mot de passe"
                        aria-describedby="password" required>
                </div>

                <div class="mb-4">
                    <a href="login.php">Déjà un compte ?</a>
                </div>

                <input type="submit" class="btn btn-primary rounded col-12" value="Créer mon compte">
            </form>
        </div>
    </div>
</div>

<?php

include "../include/_footer.php";
