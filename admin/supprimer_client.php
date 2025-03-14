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

// Récupérer l'ID du client à supprimer
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: clients.php");
    exit();
}

$client_id = $_GET['id'];

// Préparer la requête de suppression
$sql_delete = "DELETE FROM users WHERE id=?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $client_id);

// Exécuter la suppression
if ($stmt_delete->execute()) {
    // Afficher le message de succès et rediriger
    echo "<script>alert('Client supprimé avec succès'); window.location.href='clients.php';</script>";
} else {
    echo "Erreur lors de la suppression du client : " . $stmt_delete->error;
}

$stmt_delete->close();
$conn->close();
?>
