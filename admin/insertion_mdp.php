<?php
$password = '123';  // Mot de passe en clair
$hashed_password = password_hash($password, PASSWORD_BCRYPT);  // Mot de passe haché

$DB_HOST = "localhost";
$DB_NAME = "syn";
$DB_USER = "root";
$DB_PASS = "";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO admin (nom, email, mot_de_passe) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $nom, $email, $hashed_password);

$nom = 'admin';
$email = 'admin@gmail.com';

$stmt->execute();
$stmt->close();
$conn->close();
?>
