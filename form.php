<?php

include_once "_include.php";

if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

include "_header.php";

?>

<div class="w-100 h-100 d-flex align-items-center justify-content-center">
    <div class="card">
        <div class="card-header">Vérifiez votre code</div>
        <div class="card-body p-4">
            <form class="" method="post">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="course"></label>
                    <select class="form-select" id="course">
                        <option selected>Choisir un cours...</option>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <label class="input-group-text" for="exercice"></label>
                    <select class="form-select" id="exercice">
                        <option selected>Choisir un execice...</option>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <input type="file" class="form-control" id="file">
                </div>

                <input type="submit" class="btn btn-primary rounded col-12" value="Confirmer l'envoi">
            </form>
        </div>
    </div>
</div>

<?php

include "_footer.php";
