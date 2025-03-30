<?php

include_once "_include.php";

if (is_logged_in()) {
    header("location: form.php");
    exit;
}

if (isset($_POST['name'], $_POST['email'], $_POST['password'])) {
    $post = $_POST;

    if (empty($post['name']) || empty($post['email']) || empty($post['password'])) return;

    $post['name'] = trim($post['name']);
    $post['email'] = trim($post['email']);

    $query = $mysqli->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $query->bind_param(":name", $post['name']);
    $query->bind_param(":email", $post['email']);
    $query->bind_param(":password", $post['password']);

    if (!$query->execute()) return;

    $id = $query->insert_id;
    $query = $mysqli->prepare("SELECT * FROM users WHERE id = :id");
    $query->bind_param(":id", $id);
    $query->execute();
    $user = $query->fetch();

    if (!$user) return;

    login($user);

    header("location: form.php");
    exit;
}

include "_header.php";

?>

<div class="w-100 h-100 d-flex align-items-center justify-content-center">
    <div class="card">
        <div class="card-header">Création de compte</div>
        <div class="card-body p-4">
            <form class="" method="post">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-person-fill"></i></span>
                    <input type="text" class="form-control" name="name" placeholder="Nom" aria-label="Nom"
                        aria-describedby="name">
                </div>

                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="E-mail" aria-label="E-mail"
                        aria-describedby="email">
                </div>

                <div class="input-group mb-4">
                    <span class="input-group-text" id="basic-addon1"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Mot de passe" aria-label="Mot de passe"
                        aria-describedby="password">
                </div>

                <input type="submit" class="btn btn-primary rounded col-12" value="Créer mon compte">
            </form>
        </div>
    </div>
</div>

<?php

include "_footer.php";
