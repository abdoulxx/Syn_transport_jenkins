<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$host = "localhost";
$base = "syn";
$user = "root";
$pass = "";

// Connexion à la base de données
$conn = new mysqli($host, $user, $pass, $base);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer le nombre de clients
$client_sql = "SELECT COUNT(*) AS count FROM users";
$client_result = $conn->query($client_sql);
$client_count = $client_result->fetch_assoc()['count'];

// Récupérer le nombre de commandes
$commande_sql = "SELECT COUNT(*) AS count FROM commande";
$commande_result = $conn->query($commande_sql);
$commande_count = $commande_result->fetch_assoc()['count'];

$vehicule_sql = "SELECT COUNT(*) AS count FROM louer";
$vehicule_result = $conn->query($vehicule_sql);
$vehicule_count = $vehicule_result->fetch_assoc()['count'];

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <div class="navbar">
        <h1>Welcome, Admin</h1>
        <a href="admin_logout.php" class="logout-link">Logout</a>
    </div>
    <div class="container">
    <nav class="sidebar">
    <img src="logo.png" alt="Logo" class="logo">
    <ul class="menu">
        <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li>
            <a href="#"><i class="fas fa-car"></i> Véhicules <i class="fas fa-chevron-down"></i></a>
            <ul class="sub-menu">
                <li><a href="ajoutvoiture.php"><i class="fas fa-plus"></i> Ajouter Nouveau</a></li>
                <li><a href="gerer_vehicules.php"><i class="fas fa-cogs"></i> Gérer Véhicules</a></li>
            </ul>
        </li>
        <li><a href="commande.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
        <li><a href="clients.php"><i class="fas fa-users"></i> Clients</a></li>
        <li><a href="map.php"><i class="fas fa-map-marker-alt"></i> Localiser</a></li>
    </ul>
</nav>

        <div class="content">
           <div class="card-container">
    <div class="card">
        <h2><i class="fas fa-users"></i> Clients Inscrits</h2>
        <p><?php echo $client_count; ?></p>
    </div>
    <div class="card">
        <h2><i class="fas fa-shopping-cart"></i> Nombre de Commandes</h2>
        <p><?php echo $commande_count; ?></p>
    </div>
    <div class="card">
        <h2><i class="fas fa-car"></i> Véhicules Disponibles</h2>
        <p><?php echo $vehicule_count; ?></p>
    </div>
</div>

            <div class="charts-container">
                <div class="chart">
                    <h2>Aperçu des Ventes</h2>
                    <canvas id="ventesChart" width="400" height="200"></canvas>
                </div>
                <div class="chart">
                    <h2>Statut des Commandes</h2>
                    <canvas id="commandesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Données de test pour le graphique des ventes
        const ventesData = {
            labels: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin'],
            datasets: [{
                label: 'Ventes mensuelles',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                data: [65, 59, 80, 81, 56, 55],
            }]
        };

        // Options du graphique des ventes
        const ventesOptions = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        // Créer le graphique des ventes
        const ventesCtx = document.getElementById('ventesChart').getContext('2d');
        const ventesChart = new Chart(ventesCtx, {
            type: 'bar',
            data: ventesData,
            options: ventesOptions
        });

        // Données de test pour le graphique du statut des commandes
        const commandesData = {
            labels: ['En attente', 'Validées', 'Annulées'],
            datasets: [{
                label: 'Statut des Commandes',
                backgroundColor: ['#f39c12', '#2ecc71', '#e74c3c'],
                borderWidth: 1,
                data: [15, 20, 5],
            }]
        };

        // Options du graphique du statut des commandes
        const commandesOptions = {
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
                        }
                    }
                }
            }
        };

        // Créer le graphique du statut des commandes
        const commandesCtx = document.getElementById('commandesChart').getContext('2d');
        const commandesChart = new Chart(commandesCtx, {
            type: 'pie',
            data: commandesData,
            options: commandesOptions
        });
    </script>
</body>
</html>


    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            background-color: #2c3e50;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }
        .navbar .logout-link {
            color: #fff;
            text-decoration: none;
            padding: 5px 10px;
            background-color: #e74c3c;
            border-radius: 5px;
        }
        .navbar .logout-link:hover {
            background-color: #c0392b;
        }
        .container {
            display: flex;
            flex: 1;
        }
        .sidebar {
            background-color: #34495e;
            color: white;
            width: 250px;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar .logo {
            width: 100px;
            margin-bottom: 20px;
        }
        .sidebar .menu {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .sidebar .menu li {
            margin-bottom: 10px;
        }
        .sidebar .menu li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar .menu li a:hover {
            background-color: #2c3e50;
        }
        .content {
            flex: 1;
            padding: 20px;
            background-color: #ecf0f1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .content h1 {
            margin-top: 0;
        }
        .card-container {
            display: flex;
            gap: 150px;
            margin-bottom: 50px;
        }
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            flex: 1;
            text-align: center;
            max-width: 600px;
        }
        .card h2 {
            margin: 0 0 10px;
        }
        .card p {
            font-size: 24px;
            margin: 0;
        }
        .charts-container {
            display: flex;
            gap: 200px;
            width: 100%;
            justify-content: center;
        }
        .chart {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            flex: 1;
            text-align: center;
            max-width: 400px;
        }
        .sidebar {
    background-color: #34495e;
    color: white;
    width: 250px;
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center; /* Centrer le contenu verticalement */
}

.sidebar .logo {
    width: 150px; /* Ajuster la largeur du logo */
    margin-bottom: 20px;
}

    </style>