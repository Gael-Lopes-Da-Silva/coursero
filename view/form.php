<?php

include_once "../include/_include.php";

if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

if (isset($_POST['course'], $_POST['exercise'], $_POST['language'], $_FILES['file'])) {
    $uploadDir = "../uploads/submissions/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    $safeFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
    $destination = $uploadDir . $safeFileName;

    if (!move_uploaded_file($fileTmpPath, $destination)) {
        $_SESSION['notification'] = [
            "message" => "Problème lors de la récupération du fichier.",
            "type" => "danger",
        ];
        header("location: form.php?course=" . $_POST['course']);
        exit;
    }

    $status = "pending";
    $query = $mysqli->prepare("INSERT INTO submissions (user_id, course_id, exercise_id, language, file_path, status) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("iiisss", $_SESSION['user']['id'], $_POST['course'], $_POST['exercise'], $_POST['language'], $destination, $status);

    if (!$query->execute()) {
        $_SESSION['notification'] = [
            "message" => "Problème lors de l'enregistrement des informations.",
            "type" => "danger",
        ];
        header("location: form.php?course=" . $_POST['course']);
        exit;
    }

    $_SESSION['notification'] = [
        "message" => "La soumission a bien été prise en compte.",
        "type" => "success",
    ];

    header("location: form.php");
    exit;
}

if (isset($_GET['course'])) {
    $query = $mysqli->prepare("SELECT * FROM courses WHERE id = ?");
    $query->bind_param("i", $_GET['course']);
    $query->execute();
    $course = $query->get_result()->fetch_assoc();

    $query = $mysqli->prepare("SELECT * FROM exercises WHERE course_id = ?");
    $query->bind_param("i", $_GET['course']);
    $query->execute();
    $exercises = $query->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $query = $mysqli->prepare("SELECT * FROM courses");
    $query->execute();
    $courses = $query->get_result()->fetch_all(MYSQLI_ASSOC);
}

include "../include/_header.php";
include "../include/_notifs.php";

?>

<div class="position-absolute top-0 end-0 p-2">
    <a class="btn btn-primary rounded" href="../index.php?logout">Déconnexion</a>
</div>

<div class="position-absolute top-0 start-0 p-2">
    <a class="btn btn-primary rounded" href="submissions.php">Mes soumissions</a>
    <?php if ($_SESSION['user']['role'] == "admin" || $_SESSION['user']['role'] == "teacher"): ?>
        <a class="btn btn-primary rounded" href="add_courses.php">Ajouter un cours</a>
        <a class="btn btn-primary rounded" href="add_exercises.php">Ajouter un exercice</a>
    <?php endif; ?>
</div>

<div class="container w-100 h-100 mh-75 d-flex align-items-center justify-content-center">
    <?php if (!isset($_GET['course'])): ?>
        <div class="card col-6">
            <div class="card-header">Choisir un cours</div>
            <div class="card-body p-4">
                <?php if (count($courses) > 0): ?>
                    <div class="d-flex flex-column">
                        <?php foreach ($courses as $course): ?>
                            <div class="col-12">
                                <span class="text-secondary">></span>
                                <a class="" href="form.php?course=<?= $course['id'] ?>" title="<?= $course['description'] ?>"><?= $course['name'] ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center text-secondary gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <p class="m-0">Aucun cours disponnible.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="card col-6">
            <div class="card-header d-flex align-items-center gap-3">
                <a class="btn btn-primary rounded" href="form.php">Retour</a>
                <p class="m-0">Soumettre votre fichier <span class="text-secondary">></span> <?= $course['name'] ?></p>
            </div>
            <div class="card-body p-4">
                <?php if (count($exercises) > 0): ?>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="course" value="<?= $_GET['course'] ?>">

                        <div class="input-group mb-3">
                            <label class="input-group-text" for="exercise"><i class="bi bi-123"></i></label>
                            <select class="form-select" name="exercise" id="exercise" required>
                                <option value="" selected>Choisir un exercice...</option>
                                <?php foreach ($exercises as $exercise): ?>
                                    <option value="<?= $exercise['id'] ?>"><?= $exercise['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input-group mb-3">
                            <label class="input-group-text" for="language"><i class="bi bi-braces"></i></label>
                            <select class="form-select" name="language" id="language" required>
                                <option value="" selected>Choisir un language...</option>
                                <option value="C">C</option>
                                <option value="Python">Python</option>
                            </select>
                        </div>

                        <div class="input-group mb-4">
                            <input type="file" name="file" class="form-control" id="file" accept=".c,.h,.py" required>
                        </div>

                        <input type="submit" class="btn btn-primary rounded col-12" value="Soumettre">
                    </form>
                <?php else: ?>
                    <div class="d-flex align-items-center text-secondary gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <p class="m-0">Aucun exercice disponnible.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php

include "../include/_footer.php";
