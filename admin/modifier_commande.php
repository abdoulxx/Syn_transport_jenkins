<?php
// Vérifier si l'ID de la commande est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirection si l'ID n'est pas valide
    header("Location: liste_commandes.php");
    exit();
}

// Récupération de l'ID de la commande depuis les paramètres GET
$commande_id = $_GET['id'];

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

$message = ""; // Variable pour le message de confirmation

// Si le formulaire de modification est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier quel bouton a été cliqué
    if (isset($_POST['action']) && $_POST['action'] == 'update_commande') {
        // Récupérer les données du formulaire
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $nombre_jours = intval($_POST['nombre_jours']); // Convertir en entier si nécessaire
        $prix_total = floatval($_POST['prix_total']); // Convertir en décimal si nécessaire
        $mode_paiement = $_POST['mode_paiement'];
        $statut = $_POST['statut'];

        // Préparer la requête de mise à jour
        $sql_update = "UPDATE commande SET date_debut=?, date_fin=?, nombre_jours=?, prix_total=?, mode_paiement=?, statut=? WHERE id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssidsii", $date_debut, $date_fin, $nombre_jours, $prix_total, $mode_paiement, $statut, $commande_id);
        
        // Exécuter la mise à jour
        if ($stmt_update->execute()) {
            // Message de confirmation
            $message = "Modification effectuée avec succès.";
            
            // Redirection après mise à jour
            header("Location: commande.php");
            exit();
        } else {
            $message = "Erreur lors de la mise à jour de la commande : " . $stmt_update->error;
        }

        $stmt_update->close();
    }
}

// Récupérer les détails de la commande spécifiée pour afficher dans le formulaire
$sql_select = "SELECT * FROM commande WHERE id=?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $commande_id);
$stmt_select->execute();
$result = $stmt_select->get_result();

if ($result->num_rows === 1) {
    // Récupérer les données de la commande
    $commande = $result->fetch_assoc();
} else {
    // Redirection si la commande n'est pas trouvée
    header("Location: liste_commandes.php");
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
    <title>Modifier Commande</title>
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
        input[type="text"], input[type="date"], input[type="number"], select {
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
        .message {
            margin-top: 10px;
            padding: 10px;
            background-color: #dff0d8;
            border: 1px solid #c3e6cb;
            color: #3c763d;
            border-radius: 4px;
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
        <a href="commande.php">Retour au Dashboard</a>
    </div>
    <h2>Modifier Commande</h2>
    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $commande_id; ?>" method="POST">
        <input type="hidden" name="commande_id" value="<?php echo $commande['id']; ?>">
        <label for="date_debut">Date de Début :</label>
        <input type="date" id="date_debut" name="date_debut" value="<?php echo $commande['date_debut']; ?>" required>
        <label for="date_fin">Date de Fin :</label>
        <input type="date" id="date_fin" name="date_fin" value="<?php echo $commande['date_fin']; ?>" required>
        <label for="nombre_jours">Nombre de Jours :</label>
        <input type="number" id="nombre_jours" name="nombre_jours" value="<?php echo $commande['nombre_jours']; ?>" required>
        <label for="prix_total">Prix Total :</label>
        <input type="text" id="prix_total" name="prix_total" value="<?php echo $commande['prix_total']; ?>" required>
        <label for="mode_paiement">Mode de Paiement :</label>
        <select id="mode_paiement" name="mode_paiement" required>
            <option value="cash" <?php echo ($commande['mode_paiement'] === 'cash') ? 'selected' : ''; ?>>Cash</option>
            <option value="mobile_money" <?php echo ($commande['mode_paiement'] === 'mobile_money') ? 'selected' : ''; ?>>Mobile Money</option>
        </select>
        <label for="statut">Statut :</label>
        <select id="statut" name="statut" required>
            <option value="en_attente" <?php echo ($commande['statut'] === 'en_attente') ? 'selected' : ''; ?>>En Attente</option>
            <option value="valide" <?php echo ($commande['statut'] === 'valide') ? 'selected' : ''; ?>>Validé</option>
            <option value="annule" <?php echo ($commande['statut'] === 'annule') ? 'selected' : ''; ?>>Annulé</option>
        </select>
        <br><br>
        <input type="submit" name="action" value="Enregistrer">
    </form>
</body>
</html>
