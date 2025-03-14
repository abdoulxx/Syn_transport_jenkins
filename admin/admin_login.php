<?php
session_start();
$host = "localhost";
$base = "syn";
$user = "root";
$pass = "";

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connexion à la base de données
    $conn = new mysqli($host, $user, $pass, $base);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Requête pour obtenir les informations de l'administrateur par email
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Vérification du mot de passe
        if (password_verify($password, $admin['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['admin'] = $admin['email'];
            header("Location: admin_dashboard.php"); // Redirige vers le tableau de bord de l'administrateur
            exit();
        } else {
            // Mot de passe incorrect
            $error = "Invalid email or password";
        }
    } else {
        // Aucun administrateur trouvé avec cet email
        $error = "Invalid email or password";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .login-container img {
            width: 80px;
            margin-bottom: 20px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 20px);
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .login-container input[type="submit"] {
            background-color: #FF0000; /* Rouge */
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .login-container input[type="submit"]:hover {
            background-color: #CC0000; /* Rouge foncé */
        }
        .error-message {
            color: red;
            margin-top: 10px;
        }
        .admin-banner {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="logo.png" alt="Admin Icon">
        <div class="admin-banner">Espace Administrateur</div>
        <h2>Connexion Administrateur</h2>
        <form action="admin_login.php" method="POST">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="submit" value="Connexion">
        </form>
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
