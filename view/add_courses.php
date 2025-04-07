<?php

include_once "../include/_include.php";

if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

if ($_SESSION['user']['role'] != "admin" && $_SESSION['user']['role'] != "teacher") {
    header("location: form.php");
    exit;
}

if (isset($_POST['name'], $_POST['description'])) {
    $query = $mysqli->prepare("INSERT INTO courses (name, description) VALUES (?, ?)");
    $query->bind_param("ss", $_POST['name'], $_POST['description']);

    if (!$query->execute()) {
        $_SESSION['notification'] = [
            "message" => "Problème lors de l'enregistrement des informations.",
            "type" => "danger",
        ];
        header("location: add_courses.php");
        exit;
    }

    $_SESSION['notification'] = [
        "message" => "Le cours a bien été ajouté.",
        "type" => "success",
    ];

    header("location: add_courses.php");
    exit;
}

include "../include/_header.php";
include "../include/_notifs.php";

?>

<div class="position-absolute top-0 end-0 p-2">
    <a class="btn btn-primary rounded" href="../index.php?logout">Déconnexion</a>
</div>

<div class="position-absolute top-0 start-0 p-2">
    <?php if ($_SESSION['user']['role'] != "admin" || $_SESSION['user']['role'] != "teacher"): ?>
        <a class="btn btn-primary rounded" href="submissions.php">Mes soumissions</a>
    <?php endif; ?>
    <?php if ($_SESSION['user']['role'] == "admin" || $_SESSION['user']['role'] == "teacher"): ?>
        <a class="btn btn-secondary rounded" href="add_courses.php">Ajouter un cours</a>
        <a class="btn btn-primary rounded" href="add_exercises.php">Ajouter un exercice</a>
    <?php endif; ?>
</div>

<div class="container w-100 h-100 mh-75 d-flex align-items-center justify-content-center">
    <div class="card col-6">
        <div class="card-header d-flex align-items-center gap-3">
            <a class="btn btn-primary rounded" href="form.php">Retour</a>
            <p class="m-0">Ajouter un cours</p>
        </div>
        <div class="card-body p-4">
            <form method="post">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="name"><i class="bi bi-tag-fill"></i></span>
                    <input type="text" class="form-control" name="name" placeholder="Nom" aria-label="Nom" aria-describedby="name">
                </div>

                <div class="input-group mb-4">
                    <span class="input-group-text" id="description"><i class="bi bi-card-text"></i></i></span>
                    <input type="text" class="form-control" name="description" placeholder="Description" aria-label="Description" aria-describedby="description">
                </div>

                <input type="submit" class="btn btn-primary rounded col-12" value="Ajouter ce cours">
            </form>
        </div>
    </div>
</div>

<?php

include "../include/_footer.php";
