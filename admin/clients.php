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

// Récupération de tous les utilisateurs
$sql_select = "SELECT * FROM users";
$result = $conn->query($sql_select);

// Vérification de la suppression d'un utilisateur
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM users WHERE id=?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);
    $stmt_delete->execute();
    $stmt_delete->close();
    // Redirection après suppression
    header("Location: clients.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Clients</title>
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
        a {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            background-color: #4CAF50;
            border-radius: 3px;
        }
        a:hover {
            background-color: #45a049;
        }
        .action-buttons a {
            margin-right: 5px;
        }
        .top-bar {
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
    <h2>Liste des Clients</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Numéro</th>
                <th>Adresse</th>
                <th>Date de Création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nom']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['numero']; ?></td>
                        <td><?php echo $row['adresse']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td class="action-buttons">
                            <a href="modifier_client.php?id=<?php echo $row['id']; ?>">Modifier</a>
                            <a href="clients.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Aucun client trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
