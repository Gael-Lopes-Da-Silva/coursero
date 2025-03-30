<?php

include_once "_include.php";

if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

include "_header.php";

$query = $mysqli->prepare("SELECT * FROM courses");
$query->execute();
$courses = $query->fetchAll();

if (isset($_GET['course'], $_GET['exercice'], $_GET['language'], $_GET['file'])) {
    // TODO: lancer le script de vérification
}

if (isset($_GET['course'])) {
    $query = $mysqli->prepare("SELECT * FROM exercices WHERE course_id = :course_id");
    $query->bind_param(":course_id", $_GET['course']);
    $query->execute();
    $exercices = $query->fetchAll();
}
?>

<div class="w-100 h-100 d-flex align-items-center justify-content-center">
    <div class="card">
        <div class="card-header">Vérifiez votre code</div>
        <div class="card-body p-4">
            <?php if ($courses): ?>
                <?php if (isset($_GET['course'], $exercices)): ?>
                    <?php if (isset($_GET['exercice'])): ?>
                        <?php if (isset($_GET['language'])): ?>
                            <form class="" method="get">
                                <input type="hidden" name="course" value="<?= $_GET['course'] ?>">
                                <input type="hidden" name="exercice" value="<?= $_GET['exercice'] ?>">
                                <input type="hidden" name="language" value="<?= $_GET['language'] ?>">

                                <div class="input-group mb-3">
                                    <input type="file" name="file" class="form-control" id="file">
                                </div>

                                <input type="submit" class="btn btn-primary rounded col-12" value="Envoyer mon fichier">
                            </form>
                        <?php else: ?>
                            <form class="" method="get">
                                <input type="hidden" name="course" value="<?= $_GET['course'] ?>">
                                <input type="hidden" name="exercice" value="<?= $_GET['exercice'] ?>">

                                <div class="input-group mb-3">
                                    <label class="input-group-text" for="language"></label>
                                    <select class="form-select" id="language">
                                        <option selected>Choisir un language...</option>
                                        <option value="C">C</option>
                                        <option value="Python">Python</option>
                                    </select>
                                </div>

                                <input type="submit" class="btn btn-primary rounded col-12" value="Choisir ce language">
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <form class="" method="get">
                            <input type="hidden" name="course" value="<?= $_GET['course'] ?>">

                            <div class="input-group mb-3">
                                <label class="input-group-text" for="exercice"></label>
                                <select class="form-select" name="exercice" id="exercice">
                                    <option selected>Choisir un execice...</option>
                                </select>
                            </div>

                            <input type="submit" class="btn btn-primary rounded col-12" value="Choisir cet exercice">
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <form class="" method="get">
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="course"></label>
                            <select class="form-select" name="course" id="course">
                                <option selected>Choisir un cours...</option>
                            </select>
                        </div>

                        <input type="submit" class="btn btn-primary rounded col-12" value="Choisir ce cours">
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <div class="d-flex align-items-center text-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <p class="m-0">Problème lors du chargement des cours.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php

include "_footer.php";
