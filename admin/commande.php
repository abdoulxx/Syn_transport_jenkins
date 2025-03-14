<?php
// Connexion à la base de données
$host = "localhost";
$base = "syn";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $base);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les données de la table commande avec le nom du client
$sql = "SELECT c.id, u.nom AS nom_client, c.date_debut, c.date_fin, c.nombre_jours, c.prix_total, c.mode_paiement, c.date_commande, c.statut
        FROM commande c
        INNER JOIN users u ON c.client_id = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Commandes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Include FontAwesome -->

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
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
            background-color: #f9f9f9;
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
    </style>
</head>
<body>
<div class="top-bar">
        <a href="admin_dashboard.php">Retour au Dashboard</a>
    </div>
    <h2>Liste des Commandes</h2>
    <table>
        <tr>
            <th>Nom Client</th>
            <th>Date de Début</th>
            <th>Date de Fin</th>
            <th>Nombre de Jours</th>
            <th>Prix Total</th>
            <th>Mode de Paiement</th>
            <th>Date de Commande</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Afficher chaque ligne de données
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['nom_client']}</td>
                        <td>{$row['date_debut']}</td>
                        <td>{$row['date_fin']}</td>
                        <td>{$row['nombre_jours']}</td>
                        <td>{$row['prix_total']}</td>
                        <td>{$row['mode_paiement']}</td>
                        <td>{$row['date_commande']}</td>
                        <td>{$row['statut']}</td>
                        <td>
                            <a href='modifier_commande.php?id={$row['id']}'><i class='fas fa-edit' style='color: blue;'></i></a>
                            <a href='supprimer_commande.php?id={$row['id']}'><i class='fas fa-trash-alt' style='color: red;'></i></a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Aucune commande trouvée</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
