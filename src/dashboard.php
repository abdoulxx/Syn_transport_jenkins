<?php
session_start();
require "config.php";

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "etudiant") {
    header("Location: index.php");
    exit;
}

$user = $_SESSION["user"];

$stmt = $pdo->prepare("SELECT * FROM notes WHERE matricule = ?");
$stmt->execute([$user["matricule"]]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - IUA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #0066cc;
        }
        .info {
            text-align: left;
            padding: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .table th {
            background: #0066cc;
            color: white;
        }
        .logout {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: white;
            background: red;
            text-decoration: none;
            border-radius: 5px;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
            color: #007bff;
        }
    </style>
</head>
<body>

<nav class=" navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#">IUA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link" href="resultats.php">Résultats</a></li>
                <li class="nav-item"><a class="nav-link" href="a_rattraper.php">A Rattraper</a></li>
                <li class="nav-item"><a class="nav-link" href="reclamation.php">Réclamations</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1>Bienvenue, <?= $user["prenoms"] ?> !</h1>

    <div class="info">
        <p><strong>Matricule :</strong> <?= $user["matricule"] ?></p>
        <p><strong>Série du Bac :</strong> <?= $user["serie_bac"] ?></p>
        <p><strong>Filière :</strong> <?= $user["filiere"] ?></p>
        <p><strong>Niveau :</strong> <?= $user["niveau"] ?></p>
        <p><strong>Contact :</strong> <?= $user["contact"] ?></p>
        <p><strong>Sexe :</strong> <?= $user["sexe"] ?></p>
        <p><strong>Date de naissance :</strong> <?= date("d/m/Y", strtotime($user["date_naissance"])) ?></p>
    </div>

    <h2>📚 Vos recentes notes</h2>
    <table class="table">
        <tr>
            <th>Matière</th>
            <th>Note /20</th>
        </tr>
        <?php if (count($notes) > 0): ?>
            <?php foreach ($notes as $note): ?>
                <tr>
                    <td><?= $note["matiere"] ?></td>
                    <td><?= $note["note"] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">Aucune note enregistrée.</td></tr>
        <?php endif; ?>
    </table>

    <a class="logout" href="logout.php">Se déconnecter</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
