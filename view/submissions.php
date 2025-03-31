<?php

include_once "../include/_include.php";

if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

$query = $mysqli->prepare("WITH RankedSubmissions AS (SELECT *, ROW_NUMBER() OVER (PARTITION BY course_id, exercise_id, status ORDER BY score DESC) AS row_num FROM submissions WHERE user_id = ?) SELECT * FROM RankedSubmissions WHERE row_num = 1 ORDER BY status DESC");
$query->bind_param("i", $_SESSION['user']['id']);
$query->execute();
$submissions = $query->get_result()->fetch_all(MYSQLI_ASSOC);

include "../include/_header.php";

?>

<div class="position-absolute top-0 end-0 p-2">
    <a class="btn btn-primary rounded" href="../index.php?logout">Déconnexion</a>
</div>

<div class="position-absolute top-0 start-0 p-2">
    <a class="btn btn-secondary rounded" href="submissions.php">Mes soumissions</a>
    <?php if ($_SESSION['user']['role'] == "admin" || $_SESSION['user']['role'] == "teacher"): ?>
        <a class="btn btn-primary rounded" href="">Ajouter un cours</a>
        <a class="btn btn-primary rounded" href="">Ajouter un exercice</a>
    <?php endif; ?>
</div>

<div class="container w-100 h-100 mh-75 d-flex align-items-center justify-content-center">
    <div class="card col-10">
        <div class="card-header d-flex align-items-center gap-3">
            <a class="btn btn-primary rounded" href="form.php">Retour</a>
            <p class="m-0">Vos soumissions</p>
        </div>
        <div class="card-body p-4">
            <?php if (count($submissions) > 0): ?>
                <table class="table table-striped table-bordered m-0">
                    <thead>
                        <tr>
                            <th class="text-center" scope="col">Cours</th>
                            <th class="text-center" scope="col">Exercice</th>
                            <th class="text-center" scope="col">Langage</th>
                            <th class="text-center" scope="col">Score</th>
                            <th class="text-center" scope="col">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <?php
                            $query = $mysqli->prepare("SELECT * FROM courses WHERE id = ?");
                            $query->bind_param("i", $submission['course_id']);
                            $query->execute();
                            $course = $query->get_result()->fetch_assoc();

                            $query = $mysqli->prepare("SELECT * FROM exercises WHERE id = ?");
                            $query->bind_param("i", $submission['exercise_id']);
                            $query->execute();
                            $exercise = $query->get_result()->fetch_assoc();
                            ?>
                            <tr>
                                <td title="<?= $course['description'] ?>"><?= $course['name'] ?></td>
                                <td><?= $exercise['name'] ?></td>
                                <td class="text-center"><span class="badge text-bg-info"><?= $submission['language'] ?></span></td>
                                <td class="text-center"><?= !empty($submission['score']) ? $submission['score'] . "%" : "-" ?></td>
                                <td class="text-center"><span class="badge text-bg-<?= $submission['status'] == 'pending' ? "warning" : ($submission['status'] == 'running' ? "primary" : "success") ?>"><?= $submission['status'] == 'pending' ? "En attente" : ($submission['status'] == 'running' ? "En cours" : "Fini") ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="d-flex align-items-center text-secondary gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <p class="m-0">Aucunne soumissions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php

include "../include/_footer.php";
