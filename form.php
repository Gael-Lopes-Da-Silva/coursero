<?php

include_once "_include.php";

if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

include "_header.php";

if (isset($_GET['course'], $_GET['exercise'], $_GET['language'], $_GET['file'])) {
    $originalFile = $_GET['file'];
    $destinationDir = "submissions/";

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }

    $newFileName = uniqid("submission_") . "_" . basename($originalFile);
    $newFilePath = $destinationDir . $newFileName;

    if (!copy($originalFile, $newFilePath)) {
        exit("Error copying the file.");
    }

    $status = "pending";
    $query = $mysqli->prepare("INSERT INTO submissions (user_id, course_id, exercise_id, language, file_path, status) VALUES (?, ?, ?, ?, ?, ?)");
    $query->bind_param("iiisss", $_SESSION['user']['id'], $_GET['course'], $_GET['exercise'], $_GET['language'], $_GET['file'], $status);

    if (!$query->execute()) return;

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
?>

<div class="w-100 h-100 d-flex align-items-center justify-content-center">
    <?php if (!isset($_GET['course'])): ?>
        <div class="card">
            <div class="card-body">Choisir un cours</div>
            <div class="card-body">
                <?php if (count($courses) > 0): ?>
                    <div class="d-flex flex-column">
                        <?php foreach ($courses as $course): ?>
                            <a href="form.php?course=<?= $course['id'] ?>" title="<?= $course['description'] ?>"><?= $course['name'] ?></a>
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
        <div class="card">
            <div class="card-body">Soumettre votre fichier</div>
            <div class="card-body">
                <?php if (count($exercises) > 0): ?>
                    <form class="" method="get">
                        <input type="hidden" name="course" value="<?= $_GET['course'] ?>">

                        <div class="input-group mb-3">
                            <label class="input-group-text" for="exercise"></label>
                            <select class="form-select" name="exercise" id="exercise">
                                <option selected>Choisir un exercice...</option>
                                <?php foreach ($exercises as $exercise): ?>
                                    <option value="<?= $exercise['id'] ?>"><?= $exercise['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input-group mb-3">
                            <label class="input-group-text" for="language"></label>
                            <select class="form-select" name="language" id="language">
                                <option selected>Choisir un language...</option>
                                <option value="C">C</option>
                                <option value="Python">Python</option>
                            </select>
                        </div>

                        <div class="input-group mb-3">
                            <input type="file" name="file" class="form-control" id="file">
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

include "_footer.php";
