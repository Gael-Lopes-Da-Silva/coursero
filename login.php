<?php

include_once "_define.php";

if (is_logged_in()) {
    header("location: form.php");
    exit;
}

if (isset($_POST['email'], $_POST['password'])) {
}

include "_header.php";

?>

<div class="w-100 h-100 d-flex align-items-center justify-content-center">
    <div class="card">
        <div class="card-header">Connexion</div>
        <div class="card-body p-4">
            <form class="" method="post">
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

                <input type="submit" class="btn btn-primary rounded col-12" value="Se connecter">
            </form>
        </div>
    </div>
</div>

<?php

include "_footer.php";
