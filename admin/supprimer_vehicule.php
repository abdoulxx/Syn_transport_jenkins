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

// Récupérer l'ID du véhicule à supprimer
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: gerer_vehicules.php");
    exit();
}

$vehicule_id = $_GET['id'];

// Si le formulaire de suppression est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Préparer la requête de suppression
    $sql_delete = "DELETE FROM louer WHERE id=?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $vehicule_id);
    
    // Exécuter la suppression
    if ($stmt_delete->execute()) {
        // Afficher le message de succès et rediriger
        echo "<script>alert('Suppression effectuée avec succès'); window.location.href='gerer_vehicules.php';</script>";
    } else {
        echo "Erreur lors de la suppression du véhicule : " . $stmt_delete->error;
    }

    $stmt_delete->close();
}

// Récupérer les détails du véhicule à supprimer
$sql_select = "SELECT * FROM louer WHERE id=?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $vehicule_id);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows === 1) {
    $vehicule = $result->fetch_assoc();
} else {
    header("Location: gerer_vehicules.php");
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
    <title>Supprimer Véhicule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .confirmation-box {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .confirmation-box h2 {
            color: #f44336;
        }
        .confirmation-box p {
            margin: 20px 0;
        }
        .confirmation-box form {
            display: inline-block;
            margin-top: 20px;
        }
        .confirmation-box input[type="submit"], .confirmation-box a {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .confirmation-box a {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="confirmation-box">
        <h2>Supprimer Véhicule</h2>
        <p>Êtes-vous sûr de vouloir supprimer le véhicule suivant ?</p>
        <p><strong>Nom :</strong> <?php echo $vehicule['nom']; ?></p>
        <p><strong>Type de Carburant :</strong> <?php echo $vehicule['type_carburant']; ?></p>
        <p><strong>Nombre de Places :</strong> <?php echo $vehicule['nombre_places']; ?></p>
        <p><strong>Transmission :</strong> <?php echo $vehicule['transmission']; ?></p>
        <p><strong>Consommation :</strong> <?php echo $vehicule['consommation']; ?></p>
        <p><strong>Prix par Jour :</strong> <?php echo $vehicule['prix_jour']; ?></p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $vehicule_id; ?>" method="POST">
            <input type="submit" value="Supprimer">
        </form>
        <a href="gerer_vehicules.php">Annuler</a>
    </div>
</body>
</html>
