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
                        <input type="file" name="file" class="form-control" id="file" accept=".c,.h,.py" required>
                    </div>

                    <!-- Arguments -->
                    <div class="mb-3">
                        <label for="args" class="form-label fw-bold">Tests & Arguments</label>
                        <div id="args-container" class="d-flex flex-column gap-3 mb-2"></div>
                        <button type="button" id="add-test" class="btn btn-sm btn-secondary d-flex gap-2"><i class="bi bi-plus-circle-fill"></i>Ajouter un test</button>
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
    $(document).ready(function() {
        let testIndex = 0;

        $("#add-test").on("click", function() {
            const row = document.createElement('div');
            row.className = 'border rounded p-3 position-relative test-row';
            row.innerHTML = `
                <div class="arg-list d-flex flex-column gap-2 mb-2"></div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex gap-2" onclick="syncAddArg()"><i class="bi bi-plus-circle-fill"></i>Ajouter un argument</button>
                    <button type="button" class="btn btn-sm btn-danger ms-auto d-flex gap-2" onclick="this.parentElement.parentElement.remove()"><i class="bi bi-x-circle-fill"></i>Supprimer ce test</button>
                </div>
            `;

            $("#args-container").append(row);
            syncToLongestArgs();
        });
    });

    function syncAddArg() {
        document.querySelectorAll('.test-row').forEach((row, i) => {
            const argList = row.querySelector('.arg-list');
            const testNum = i;
            const argItem = document.createElement('div');
            argItem.className = 'input-group arg-item';
            argItem.innerHTML = `
                <input type="text" class="form-control" name="args[${testNum}][]" placeholder="Argument">
                <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash-fill"></i></button>
            `;
            argList.appendChild(argItem);
        });
    }

    function syncToLongestArgs() {
        const allRows = document.querySelectorAll('.test-row');
        if (allRows.length === 0) return;

        let maxArgs = 0;
        allRows.forEach(row => {
            const count = row.querySelectorAll('.arg-item').length;
            if (count > maxArgs) maxArgs = count;
        });

        allRows.forEach((row, i) => {
            const argList = row.querySelector('.arg-list');
            while (argList.children.length < maxArgs) {
                const argItem = document.createElement('div');
                argItem.className = 'input-group arg-item';
                argItem.innerHTML = `
                    <input type="text" class="form-control" name="args[${i}][]" placeholder="Argument">
                    <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash-fill"></i></button>
                `;
                argList.appendChild(argItem);
            }
        });
    }
</script>

<?php include "../include/_footer.php"; ?>
