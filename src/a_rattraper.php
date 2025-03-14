<?php
session_start();
require "config.php";

if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "etudiant") {
    header("Location: index.php");
    exit;
}

$user = $_SESSION["user"];

$stmt = $pdo->prepare("SELECT * FROM notes WHERE matricule = ? AND note < 10");
$stmt->execute([$user["matricule"]]);
$notes_a_rattraper = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rattrapage - IUA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: red;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            margin-top: 10px;
        }
        .table th, .table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .table th {
            background: #dc3545;
            color: white;
        }
        .note-rouge {
            color: red;
            font-weight: bold;
        }
        .message {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: red;
            margin-bottom: 20px;
        }
    </style>
       </style>
        <style>
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
    <div class="header">
        📢 ATTENTION : Vous avez des matières à rattraper ❌
    </div>

    <?php if (count($notes_a_rattraper) > 0) : ?>
        <p class="message">Vous n'avez pas obtenu le nombre de points nécessaires pour valider les matières ci-dessous.</p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Note /20</th>
                    <th>État</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($notes_a_rattraper as $note) : ?>
                    <tr>
                        <td><?= $note["matiere"] ?></td>
                        <td class="note-rouge"><?= number_format($note["note"], 2) ?></td>
                        <td class="note-rouge">🔴 À RATTRAPER</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p class="text-center text-success fw-bold">✅ Félicitations, vous n'avez aucune matière à rattraper !</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
