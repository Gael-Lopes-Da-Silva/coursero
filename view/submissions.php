<?php

include_once "../include/_include.php";

if (!is_logged_in()) {
    header("location: login.php");
    exit;
}

$query = $mysqli->prepare("SELECT * FROM submissions WHERE user_id = ?");
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
    <div class="card col-6">
        <div class="card-header d-flex align-items-center gap-3">
            <a class="btn btn-primary rounded" href="form.php">Retour</a>
            <p class="m-0">Vos soumissions</p>
        </div>
        <div class="card-body p-4">
            <?php if (count($submissions) > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Cours</th>
                            <th scope="col">Exercice</th>
                            <th scope="col">Langage</th>
                            <th scope="col">Score</th>
                            <th scope="col">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?= $submission['course_id'] ?></td>
                                <td><?= $submission['exercise_id'] ?></td>
                                <td><?= $submission['language'] ?></td>
                                <td><?= $submission['score'] ?></td>
                                <td><?= $submission['status'] ?></td>
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
