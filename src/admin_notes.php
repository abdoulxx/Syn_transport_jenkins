<?php
session_start();
require "config.php";

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE filiere = 'Informatique'");
$stmt->execute();
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM matieres WHERE filiere = 'Informatique'");
$stmt->execute();
$matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$notes_etudiant = [];
$message = "";
if (isset($_GET["matricule"])) {
    $matricule = $_GET["matricule"];

    $stmt = $pdo->prepare("SELECT * FROM notes WHERE matricule = ?");
    $stmt->execute([$matricule]);
    $notes_existantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($notes_existantes as $note) {
        $notes_etudiant[$note["matiere"]] = $note["note"];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $matricule = $_POST["matricule"];

    if (isset($_POST["reset_notes"])) {
        $stmt = $pdo->prepare("DELETE FROM notes WHERE matricule = ?");
        $stmt->execute([$matricule]);
        $message = "✅ Toutes les notes ont été supprimées avec succès.";
    } else {
        foreach ($_POST["notes"] as $matiere => $note) {
            if ($note !== "") {
                $stmt = $pdo->prepare("SELECT * FROM notes WHERE matricule = ? AND matiere = ?");
                $stmt->execute([$matricule, $matiere]);
                $note_existante = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($note_existante) {
                    $stmt = $pdo->prepare("UPDATE notes SET note = ? WHERE matricule = ? AND matiere = ?");
                    $stmt->execute([$note, $matricule, $matiere]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO notes (matricule, matiere, note) VALUES (?, ?, ?)");
                    $stmt->execute([$matricule, $matiere, $note]);
                }
            }
        }
        $message = "✅ Les notes ont été enregistrées avec succès.";
    }

    header("Location: admin_notes.php?matricule=$matricule&message=" . urlencode($message));
    exit;
}

if (isset($_GET["message"])) {
    $message = $_GET["message"];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes - IUA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
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
    </style>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Admin IUA</a>
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
    <h2 class="text-center mb-4">Gestion des Notes - Filière Informatique</h2>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-success text-center"><?= $message ?></div>
    <?php endif; ?>

    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <select name="matricule" class="form-control" required>
                    <option value="">Sélectionner un étudiant</option>
                    <?php foreach ($etudiants as $etudiant) : ?>
                        <option value="<?= $etudiant["matricule"] ?>" <?= (isset($_GET["matricule"]) && $_GET["matricule"] == $etudiant["matricule"]) ? 'selected' : '' ?>>
                            <?= $etudiant["prenoms"] . " - " . $etudiant["matricule"] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Afficher</button>
            </div>
        </div>
    </form>

    <?php if (isset($_GET["matricule"])) : ?>
        <form method="POST">
            <input type="hidden" name="matricule" value="<?= $_GET["matricule"] ?>">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Note /20</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matieres as $matiere) : ?>
                        <tr>
                            <td><?= $matiere["nom"] ?></td>
                            <td>
                                <input type="number" name="notes[<?= $matiere["nom"] ?>]" class="form-control"
                                       step="0.01" min="0" max="20"
                                       value="<?= isset($notes_etudiant[$matiere["nom"]]) ? $notes_etudiant[$matiere["nom"]] : '' ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn btn-success w-100">💾 Enregistrer les Notes</button>
        </form>

        <form method="POST" class="mt-3">
            <input type="hidden" name="matricule" value="<?= $_GET["matricule"] ?>">
            <button type="submit" name="reset_notes" class="btn btn-danger w-100">🗑 Supprimer Toutes les Notes</button>
        </form>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
