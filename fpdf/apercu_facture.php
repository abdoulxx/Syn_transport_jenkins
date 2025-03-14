<?php
require('fpdf.php');

session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit();
}

// Récupérer l'ID de la commande
if (!isset($_GET['id'])) {
    die("ID de la commande manquant.");
}

$commande_id = $_GET['id'];
$client_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "syn";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les détails de la commande
$sql = "SELECT c.id, c.date_debut, c.date_fin, c.nombre_jours, c.prix_total, c.mode_paiement, c.statut, l.nom AS nom_voiture, c.photo_voiture, u.nom AS client_nom, u.email AS client_email, u.numero AS client_numero, u.adresse AS client_adresse
        FROM commande c
        INNER JOIN louer l ON c.voiture_id = l.id
        INNER JOIN users u ON c.client_id = u.id
        WHERE c.client_id = ? AND c.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $client_id, $commande_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Commande non trouvée.");
}

$commande = $result->fetch_assoc();
$stmt->close();

// Récupérer les détails du client connecté
$sql_client = "SELECT nom, email, adresse, numero FROM users WHERE id = ?";
$stmt_client = $conn->prepare($sql_client);
$stmt_client->bind_param("i", $client_id);
$stmt_client->execute();
$result_client = $stmt_client->get_result();

if ($result_client->num_rows === 0) {
    die("Détails du client non trouvés.");
}

$client_details = $result_client->fetch_assoc();
$stmt_client->close();

// Création d'un nouveau document PDF (Portrait, en mm, taille A4)
$pdf = new FPDF('P', 'mm', 'A4');

// Ajouter une nouvelle page
$pdf->AddPage();

// Entête
$pdf->Image('logo.png', 10, 5, 60); // Déplacement du logo un peu plus haut en réduisant la coordonnée Y

// Décalage vers la droite pour aligner le texte à droite
$pdf->Cell(0, 10, '', 0, 0); // Cellule vide pour décalage
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'SYN Transport', 0, 1, 'R'); // Aligné à droite

$pdf->Cell(0, 10, '', 0, 0); // Cellule vide pour décalage
$pdf->Cell(0, 10, 'Adresse: Marcory injs, Abidjan, Cote dIvoire', 0, 1, 'R'); // Aligné à droite

$pdf->Cell(0, 10, '', 0, 0); // Cellule vide pour décalage
$pdf->Cell(0, 10, 'Telephone: +225 01 51 51 60 84', 0, 1, 'R'); // Aligné à droite

$pdf->Cell(0, 10, '', 0, 0); // Cellule vide pour décalage
$pdf->Cell(0, 10, 'Email: contact@syn_transport.com', 0, 1, 'R'); // Aligné à droite

$pdf->Ln(5);

// Obtenez la date du jour
// Obtenez la date du jour
$date_facture = date('d/m/Y');

// Espacement entre la date de la facture et l'ID de commande
$espace = 20;

// Afficher la date de la facture
$pdf->Cell(0, 10, 'Date de la facture: ' . $date_facture, 0, 0);

// Ajouter un espacement
$pdf->Cell($espace);

// Afficher l'ID de la commande
$pdf->Cell(0, 10, 'Commande ID: ' . $commande['id'], 0, 1, 'R');


// Titre
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'FACTURE DE LOCATION DE VEHICULE', 'TB', 1, 'C');
$pdf->Ln(5);

// Détails de la location
// Tableau des détails de la location
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Details de la location:', 0, 1);

// Début du tableau
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Attribut', 1, 0, 'C');
$pdf->Cell(0, 10, 'Valeur', 1, 1, 'C');

$pdf->SetFont('Arial', '', 12);
// Affichage des détails de la location dans le tableau
$pdf->Cell(60, 10, 'Nom de la Voiture:', 1, 0);
$pdf->Cell(0, 10, $commande['nom_voiture'], 1, 1);

$pdf->Cell(60, 10, 'Date de Debut:', 1, 0);
$pdf->Cell(0, 10, $commande['date_debut'], 1, 1);

$pdf->Cell(60, 10, 'Date de Fin:', 1, 0);
$pdf->Cell(0, 10, $commande['date_fin'], 1, 1);

$pdf->Cell(60, 10, 'Nombre de Jours:', 1, 0);
$pdf->Cell(0, 10, $commande['nombre_jours']. ' Jours', 1, 1);

$pdf->Cell(60, 10, 'Prix Total:', 1, 0);
$pdf->Cell(0, 10, number_format($commande['prix_total'], 2) . ' CFA', 1, 1);

$pdf->Cell(60, 10, 'Mode de Paiement:', 1, 0);
$pdf->Cell(0, 10, $commande['mode_paiement'], 1, 1);

// Fin du tableau
$pdf->Ln(10);

// Informations du client
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Informations du client', 0, 1);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Nom: ' . $client_details['nom'], 0, 1);
$pdf->Cell(0, 10, 'Email: ' . $client_details['email'], 0, 1);
$pdf->Cell(0, 10, 'Adresse: ' . $client_details['adresse'], 0, 1);
$pdf->Cell(0, 10, 'Numero de Telephone: ' . $client_details['numero'], 0, 1);

$pdf->Ln(10);

// Signatures
// Set the text color to red
$pdf->SetTextColor(255, 0, 0);

// Write the text
$pdf->Cell(0, 10, 'Je soussigne le client avoir pris connaissance des conditions generales de location de voiture', 0, 1);

// Reset text color to black
$pdf->SetTextColor(0, 0, 0);



// Afficher le PDF dans le navigateur
$pdf->Output();
?>
