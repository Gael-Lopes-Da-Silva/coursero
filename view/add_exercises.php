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

if (isset($_POST['course'], $_POST['name'], $_FILES['file'])) {
    $uploadDir = "../uploads/exercises/references/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    $safeFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
    $destination = $uploadDir . $safeFileName;

    if (!move_uploaded_file($fileTmpPath, $destination)) return;

    $query = $mysqli->prepare("INSERT INTO exercises (course_id, name, reference_file) VALUES (?, ?, ?)");
    $query->bind_param("iss", $_POST['course'], $_POST['name'], $destination);

    if (!$query->execute()) return;

    header("location: form.php");
    exit;
}

$query = $mysqli->prepare("SELECT * FROM courses");
$query->execute();
$courses = $query->get_result()->fetch_all(MYSQLI_ASSOC);

include "../include/_header.php";

?>

<div class="position-absolute top-0 end-0 p-2">
    <a class="btn btn-primary rounded" href="../index.php?logout">Déconnexion</a>
</div>

<div class="position-absolute top-0 start-0 p-2">
    <a class="btn btn-primary rounded" href="submissions.php">Mes soumissions</a>
    <?php if ($_SESSION['user']['role'] == "admin" || $_SESSION['user']['role'] == "teacher"): ?>
        <a class="btn btn-primary rounded" href="add_courses.php">Ajouter un cours</a>
        <a class="btn btn-secondary rounded" href="add_exercises.php">Ajouter un exercice</a>
    <?php endif; ?>
</div>

<div class="container w-100 h-100 mh-75 d-flex align-items-center justify-content-center">
    <div class="card col-6">
        <div class="card-header d-flex align-items-center gap-3">
            <a class="btn btn-primary rounded" href="form.php">Retour</a>
            <p class="m-0">Ajouter un exercice</p>
        </div>
        <div class="card-body p-4">
            <?php if (count($courses) > 0): ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="course"><i class="bi bi-mortarboard-fill"></i></i></label>
                        <select class="form-select" name="course" id="course">
                            <option selected>Choisir un cours...</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= $course['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="name"><i class="bi bi-tag-fill"></i></span>
                        <input type="text" class="form-control" name="name" placeholder="Nom" aria-label="Nom" aria-describedby="name">
                    </div>

                    <div class="input-group mb-4">
                        <input type="file" name="file" class="form-control" id="file">
                    </div>

                    <input type="submit" class="btn btn-primary rounded col-12" value="Ajouter cet exercice">
                </form>
            <?php else: ?>
                <div class="d-flex align-items-center text-secondary gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <p class="m-0">Aucun cours disponnible.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php

include "../include/_footer.php";
