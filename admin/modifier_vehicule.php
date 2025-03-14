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

// Récupérer l'ID du véhicule à modifier
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: gerer_vehicules.php");
    exit();
}

$vehicule_id = $_GET['id'];

// Si le formulaire de modification est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $type_carburant = $_POST['type_carburant'];
    $nombre_places = $_POST['nombre_places'];
    $transmission = $_POST['transmission'];
    $consommation = $_POST['consommation'];
    $prix_jour = $_POST['prix_jour'];

    // Préparer la requête de mise à jour
    $sql_update = "UPDATE louer SET nom=?, type_carburant=?, nombre_places=?, transmission=?, consommation=?, prix_jour=? WHERE id=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssissdi", $nom, $type_carburant, $nombre_places, $transmission, $consommation, $prix_jour, $vehicule_id);
    
    // Exécuter la mise à jour
    if ($stmt_update->execute()) {
        // Afficher le message de succès et rediriger
        echo "<script>alert('Modification effectuée avec succès'); window.location.href='gerer_vehicules.php';</script>";
    } else {
        echo "Erreur lors de la mise à jour du véhicule : " . $stmt_update->error;
    }

    $stmt_update->close();
}

// Récupérer les détails du véhicule à modifier
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
    <title>Modifier Véhicule</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .top-bar a {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="number"], select {
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
    <div class="top-bar">
        <h2>Modifier Véhicule</h2>
        <a href="dashboard.php">Retour au Dashboard</a>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $vehicule_id; ?>" method="POST">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo $vehicule['nom']; ?>" required>
        <label for="type_carburant">Type de Carburant :</label>
        <input type="text" id="type_carburant" name="type_carburant" value="<?php echo $vehicule['type_carburant']; ?>" required>
        <label for="nombre_places">Nombre de Places :</label>
        <input type="number" id="nombre_places" name="nombre_places" value="<?php echo $vehicule['nombre_places']; ?>" required>
        <label for="transmission">Transmission :</label>
        <input type="text" id="transmission" name="transmission" value="<?php echo $vehicule['transmission']; ?>" required>
        <label for="consommation">Consommation :</label>
        <input type="text" id="consommation" name="consommation" value="<?php echo $vehicule['consommation']; ?>" required>
        <label for="prix_jour">Prix par Jour :</label>
        <input type="number" step="0.01" id="prix_jour" name="prix_jour" value="<?php echo $vehicule['prix_jour']; ?>" required>
        <br><br>
        <input type="submit" value="Modifier">
    </form>
</body>
</html>
