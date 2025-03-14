<?php
session_start();
require "config.php";

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$user = $_SESSION["user"];

$stmt = $pdo->prepare("SELECT * FROM reclamations WHERE statut = 'En cours' ORDER BY date_soumission DESC");
$stmt->execute();
$reclamations_admin = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM reclamations WHERE statut = 'Résolue' ORDER BY date_soumission DESC");
$stmt->execute();
$reclamations_resolues = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["resolve_reclamation"])) {
    $reclamation_id = $_POST["reclamation_id"];
    $stmt = $pdo->prepare("UPDATE reclamations SET statut = 'Résolue' WHERE id = ?");
    $stmt->execute([$reclamation_id]);
    header("Location: admin_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IUA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
        }
        .navbar {
            background-color: white !important;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }
        .navbar-nav .nav-link {
            font-weight: bold;
            color: #333;
            margin: 0 10px;
            transition: color 0.3s ease-in-out;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .active {
            color: #007bff !important;
        }
        .logout-btn {
            color: red !important;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .statut-en-cours {
            color: orange;
            font-weight: bold;
        }
        .statut-resolue {
            color: green;
            font-weight: bold;
        }
        .table-container {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="admin_dashboard.php">Admin IUA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">🔄 Retour</a></li>
                <li class="nav-item"><a class="nav-link logout-btn" href="logout.php">🚪 Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5" style="padding-top: 70px;">
    <h2 class="text-center mb-4">Tableau de Bord Administrateur</h2>

    <div class="card p-3 text-center">
        <p><strong>Bienvenue, <?= $user["prenoms"] ?> !</strong></p>
        <p><strong>Matricule :</strong> <?= $user["matricule"] ?></p>
        <p><strong>Rôle :</strong> Administrateur</p>
        <a href="admin_notes.php" class="btn btn-primary">Gérer les Notes</a>
    </div>

    <div class="table-container">
        <div class="box text-center">
            <h3 class="mt-3 text-danger">📌 Réclamations en attente</h3>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Sujet</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($reclamations_admin) > 0): ?>
                        <?php foreach ($reclamations_admin as $reclamation) : ?>
                            <tr>
                                <td><?= $reclamation["matricule"] ?></td>
                                <td><?= htmlspecialchars($reclamation["sujet"]) ?></td>
                                <td><?= htmlspecialchars($reclamation["message"]) ?></td>
                                <td><?= date("d/m/Y H:i", strtotime($reclamation["date_soumission"])) ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="reclamation_id" value="<?= $reclamation["id"] ?>">
                                        <button type="submit" name="resolve_reclamation" class="btn btn-success">✔ Marquer Résolue</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center text-success">✅ Aucune réclamation en attente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h3 class="mt-4 text-primary">📜 Historique des Réclamations</h3>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Matricule</th>
                        <th>Sujet</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($reclamations_resolues) > 0): ?>
                        <?php foreach ($reclamations_resolues as $reclamation) : ?>
                            <tr>
                                <td><?= $reclamation["matricule"] ?></td>
                                <td><?= htmlspecialchars($reclamation["sujet"]) ?></td>
                                <td><?= htmlspecialchars($reclamation["message"]) ?></td>
                                <td><?= date("d/m/Y H:i", strtotime($reclamation["date_soumission"])) ?></td>
                                <td class="statut-resolue">✔ Résolue</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">🔍 Aucun historique de réclamations.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
