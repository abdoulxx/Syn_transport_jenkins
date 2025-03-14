<?php
session_start();
require "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matricule = strtoupper(trim($_POST["matricule"])); 
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE matricule = ?");
    $stmt->execute([$matricule]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["mot_de_passe"])) {
        $_SESSION["user"] = $user;

        if ($user["role"] === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = "Matricule ou mot de passe incorrect.";
    }
}
?>




<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur - IUA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap Icons -->
    <style>
        body {
            background-image: url('assets/back.png');
            background-size: cover;
            background-position: center;
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background-color: #ffffff; 
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            text-align: center;
            /* Centrer le logo */
        }

        .btn-custom {
            background-color:rgb(52, 94, 185);
            color: #fff;
            border-radius: 30px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color:rgb(0, 119, 230);
        }

        .alert {
            font-size: 1rem;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .login-logo {
            width: 150px;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <img src="assets/iua.png" alt="Logo IUA" class="login-logo"> 

        <h2>Connexion <i class="bi bi-lock-fill"></i> </h2>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="mb-3">
                <input type="text" name="matricule" placeholder="Matricule" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Mot de passe" required> 
            </div>
            <button type="submit" class="btn btn-custom w-100">Se connecter</button>
            <a href="register.php">S'inscrire</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>