<?php
require "config.php";

$nom = "Admin";
$prenoms = "Super";
$date_naissance = "1990-01-01";
$pays = "CIV";
$sexe = "M";
$contact = "0102030405";
$matricule = "ADMIN12345"; 
$serie_bac = "N/A";
$filiere = "Administration";
$niveau = "N/A";
$password = password_hash("admin1234", PASSWORD_DEFAULT); 
$role = "admin";

try {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenoms, date_naissance, pays, sexe, contact, matricule, serie_bac, filiere, niveau, mot_de_passe, role) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([$nom, $prenoms, $date_naissance, $pays, $sexe, $contact, $matricule, $serie_bac, $filiere, $niveau, $password, $role]);

    echo "✅ Admin créé avec succès !";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
