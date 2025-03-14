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

// Requête pour sélectionner toutes les lignes de la table `louer`
$sql = "SELECT * FROM louer";
$result = $conn->query($sql);

// Fonction pour supprimer un véhicule
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM louer WHERE id=?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    // Redirection après suppression
    header("Location: gerer_vehicules.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <div class="top-bar">
        <a href="admin_dashboard.php">Retour au Dashboard</a>
    </div>
    <title>Gérer les Véhicules</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
        }
        .edit-button {
            background-color: #4CAF50;
        }
        .delete-button {
            background-color: #f44336;
        }
    </style>
</head>
<body>

    <h2>Liste des Véhicules à Louer</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Type de Carburant</th>
                <th>Nombre de Places</th>
                <th>Transmission</th>
                <th>Consommation</th>
                <th>Prix par Jour</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Vérifier si des résultats ont été trouvés
            if ($result->num_rows > 0) {
                // Afficher chaque ligne de la table
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nom"] . "</td>";
                    echo "<td>" . $row["type_carburant"] . "</td>";
                    echo "<td>" . $row["nombre_places"] . "</td>";
                    echo "<td>" . $row["transmission"] . "</td>";
                    echo "<td>" . $row["consommation"] . "</td>";
                    echo "<td>" . $row["prix_jour"] . "</td>";
                    echo "<td class='action-buttons'>
                            <a class='edit-button' href='modifier_vehicule.php?id=" . $row["id"] . "'>Modifier</a>
                            <a class='delete-button' href='gerer_vehicules.php?delete_id=" . $row["id"] . "' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule?');\">Supprimer</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Aucun véhicule trouvé</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
