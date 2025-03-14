<?php
// Vérifier si l'ID de la commande est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirection si l'ID n'est pas valide
    header("Location: commande.php");
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

// Préparer la requête de suppression
$sql_delete = "DELETE FROM commande WHERE id=?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $commande_id);

// Exécuter la suppression
if ($stmt_delete->execute()) {
    // Redirection après suppression avec message de succès
    echo "<script>alert('Commande supprimée avec succès'); window.location.href='commande.php';</script>";
    exit();
} else {
    echo "Erreur lors de la suppression de la commande : " . $stmt_delete->error;
}

$stmt_delete->close();
$conn->close();
?>
