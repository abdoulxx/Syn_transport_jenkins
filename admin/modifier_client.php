<?php
// Connexion à la base de données
$host = "localhost";
$base = "syn";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $base);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer l'ID du client à modifier
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: clients.php");
    exit();
}

$client_id = $_GET['id'];

// Si le formulaire de modification est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $numero = $_POST['numero'];
    $adresse = $_POST['adresse'];

    // Préparer la requête de mise à jour
    $sql_update = "UPDATE users SET nom=?, email=?, numero=?, adresse=? WHERE id=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $nom, $email, $numero, $adresse, $client_id);
    
    // Exécuter la mise à jour
    if ($stmt_update->execute()) {
        // Afficher le message de succès et rediriger
        echo "<script>alert('Modification effectuée avec succès'); window.location.href='clients.php';</script>";
    } else {
        echo "Erreur lors de la mise à jour du client : " . $stmt_update->error;
    }

    $stmt_update->close();
}

// Récupérer les détails du client à modifier
$sql_select = "SELECT * FROM users WHERE id=?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $client_id);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows === 1) {
    $client = $result->fetch_assoc();
} else {
    header("Location: clients.php");
    exit();
}

$stmt_select->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Client</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="email"], input[type="number"], textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Modifier Client</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $client_id; ?>" method="POST">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo $client['nom']; ?>" required>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?php echo $client['email']; ?>" required>
        <label for="numero">Numéro :</label>
        <input type="text" id="numero" name="numero" value="<?php echo $client['numero']; ?>">
        <label for="adresse">Adresse :</label>
        <textarea id="adresse" name="adresse"><?php echo $client['adresse']; ?></textarea>
        <br><br>
        <input type="submit" value="Modifier">
    </form>
</body>
</html>
