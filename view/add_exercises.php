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

    if (!move_uploaded_file($fileTmpPath, $destination)) {
        $_SESSION['notification'] = [
            "message" => "Problème lors de la récupération du fichier.",
            "type" => "danger",
        ];
        header("location: add_exercises.php");
        exit;
    }

    // Traitement des arguments
    $args = [];

    if (isset($_POST['args']) && is_array($_POST['args'])) {
        foreach ($_POST['args'] as $group) {
            if (is_array($group) && count(array_filter($group)) > 0) {
                $args[] = array_values(array_filter($group));
            }
        }
    }

    $args_json = json_encode($args);

    // Insertion en base
    $query = $mysqli->prepare("INSERT INTO exercises (course_id, name, reference_file, args) VALUES (?, ?, ?, ?)");
    $query->bind_param("isss", $_POST['course'], $_POST['name'], $destination, $args_json);

    if (!$query->execute()) {
        $_SESSION['notification'] = [
            "message" => "Problème lors de l'enregistrement des informations.",
            "type" => "danger",
        ];
        header("location: add_exercises.php");
        exit;
    }

    $_SESSION['notification'] = [
        "message" => "L'exercice a bien été ajouté.",
        "type" => "success",
    ];

    header("location: add_exercises.php");
    exit;
}

$query = $mysqli->prepare("SELECT * FROM courses");
$query->execute();
$courses = $query->get_result()->fetch_all(MYSQLI_ASSOC);

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
                        <label class="input-group-text" for="course"><i class="bi bi-mortarboard-fill"></i></label>
                        <select class="form-select" name="course" id="course" required>
                            <option selected disabled>Choisir un cours...</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>"><?= $course['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="name"><i class="bi bi-tag-fill"></i></span>
                        <input type="text" class="form-control" name="name" placeholder="Nom de l'exercice" required>
                    </div>

                    <div class="input-group mb-3">
                        <input type="file" name="file" class="form-control" required>
                    </div>

                    <!-- Arguments -->
                    <div class="mb-3">
                        <label for="args">Arguments de test</label>
                        <div id="args-container">
                            <!-- Première ligne par défaut -->
                            <div class="mb-2 test-row">
                                <div class="input-group mb-1">
                                    <input type="text" class="form-control" name="args[0][]" placeholder="Argument">
                                    <input type="text" class="form-control" name="args[0][]" placeholder="Argument">
                                    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">✕ Supprimer le test</button>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addArg(this)">+ Ajouter un argument</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addTestRow()">+ Ajouter un test</button>
                    </div>

                    <input type="submit" class="btn btn-primary rounded col-12" value="Ajouter cet exercice">
                </form>
            <?php else: ?>
                <div class="d-flex align-items-center text-secondary gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <p class="m-0">Aucun cours disponible.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
let testIndex = 1;

function addTestRow() {
    const container = document.getElementById('args-container');
    const row = document.createElement('div');
    row.className = 'mb-2 test-row';
    row.innerHTML = `
        <div class="input-group mb-1">
            <input type="text" class="form-control" name="args[${testIndex}][]" placeholder="Argument">
            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">✕ Supprimer le test</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addArg(this)">+ Ajouter un argument</button>
    `;
    container.appendChild(row);
    testIndex++;
}

function addArg(button) {
    const group = button.previousElementSibling;
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control';
    input.placeholder = 'Argument';

    const testNum = group.querySelector('input').name.match(/\d+/)[0];
    input.name = `args[${testNum}][]`;

    group.insertBefore(input, button);
}
</script>

<?php include "../include/_footer.php"; ?>
