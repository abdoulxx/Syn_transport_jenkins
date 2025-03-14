<?php
session_start();

// Vérifier si l'utilisateur est administrateur
/*if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas administrateur
    header('Location: connexion.php');
    exit();
}*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $host = "localhost";
$base = "syn";
$user = "root";
$pass = "";
$conn = new PDO("mysql:host=$host;dbname=$base", $user, $pass);


        $nom = $_POST['nom'];
        $type_carburant = $_POST['type_carburant'];
        $nombre_places = $_POST['nombre_places'];
        $transmission = $_POST['transmission'];
        $consommation = $_POST['consommation'];
        $prix_jour = $_POST['prix_jour'];

        $sql = "INSERT INTO louer (nom, type_carburant, nombre_places, transmission, consommation, prix_jour) 
                VALUES (:nom, :type_carburant, :nombre_places, :transmission, :consommation, :prix_jour)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':type_carburant', $type_carburant);
        $stmt->bindParam(':nombre_places', $nombre_places, PDO::PARAM_INT);
        $stmt->bindParam(':transmission', $transmission);
        $stmt->bindParam(':consommation', $consommation, PDO::PARAM_INT);
        $stmt->bindParam(':prix_jour', $prix_jour, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $voiture_id = $conn->lastInsertId();

            // Gestion des fichiers téléchargés
            $target_dir = "uploads/";
            $is_cover = true; // Mark the first image as the cover photo
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $target_file = $target_dir . basename($_FILES['images']['name'][$key]);
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $image_url = $target_file;
                    $sql_image = "INSERT INTO images (voiture_id, image_url, is_cover) VALUES (:voiture_id, :image_url, :is_cover)";
                    $stmt_image = $conn->prepare($sql_image);
                    $stmt_image->bindParam(':voiture_id', $voiture_id, PDO::PARAM_INT);
                    $stmt_image->bindParam(':image_url', $image_url);
                    $stmt_image->bindParam(':is_cover', $is_cover, PDO::PARAM_BOOL);
                    $stmt_image->execute();
                    $is_cover = false; // Subsequent images are not cover photos
                }
            }

            echo "Nouvelle voiture ajoutée avec succès.";
        } else {
            echo "Erreur lors de l'ajout de la voiture.";
        }

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }

    // Close the PDO connection
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une voiture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter une nouvelle voiture</h2>
        <form action="ajoutvoiture.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom de la voiture</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="mb-3">
                <label for="type_carburant" class="form-label">Type de carburant</label>
                <input type="text" class="form-control" id="type_carburant" name="type_carburant" required>
            </div>
            <div class="mb-3">
                <label for="nombre_places" class="form-label">Nombre de places</label>
                <input type="number" class="form-control" id="nombre_places" name="nombre_places" required>
            </div>
            <div class="mb-3">
                <label for="transmission" class="form-label">Transmission</label>
                <input type="text" class="form-control" id="transmission" name="transmission" required>
            </div>
            <div class="mb-3">
                <label for="consommation" class="form-label">Consommation (Km/h)</label>
                <input type="number" class="form-control" id="consommation" name="consommation" required>
            </div>
            <div class="mb-3">
                <label for="prix_jour" class="form-label">Prix par jour</label>
                <input type="number" class="form-control" id="prix_jour" name="prix_jour" required>
            </div>
            <div class="mb-3">
                <label for="images" class="form-label">Images de la voiture (vous pouvez sélectionner plusieurs images)</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
