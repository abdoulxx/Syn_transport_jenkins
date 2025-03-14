<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localisation des Véhicules avec Leaflet.js et OpenStreetMap</title>
    <!-- Inclure Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map { height: 600px; }
        .legend { margin: 10px; }
        .legend-item { margin-right: 10px; }
        .legend-item span { display: inline-block; width: 15px; height: 15px; margin-right: 5px; }
    </style>
</head>
<body>
    <h1>Localisation des Véhicules</h1>
    <div id="map"></div>

    <div class="legend">
        <div class="legend-item"><span style="background-color: red;"></span>Range rover</div>
        <div class="legend-item"><span style="background-color: blue;"></span>tucson</div>
        <div class="legend-item"><span style="background-color: green;"></span>mitsubishi</div>
        <div class="legend-item"><span style="background-color: yellow;"></span>bmw x3</div>
        <div class="legend-item"><span style="background-color: purple;"></span>honda crv</div>
    </div>

    <script>
        // Initialisation de la carte avec Leaflet.js
        var map = L.map('map').setView([5.359952, -4.008256], 12); // Coordonnées d'Abidjan

        // Ajout de la couche OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        // Exemple de marqueurs avec des couleurs différentes
        var vehicles = [
            { name: 'Range rover', latitude: 5.360, longitude: -4.009, color: 'red' },
            { name: 'tucson ', latitude: 5.361, longitude: -4.010, color: 'blue' },
            { name: 'mitsubishi', latitude: 5.362, longitude: -4.011, color: 'green' },
            { name: 'bmw x3', latitude: 5.363, longitude: -4.012, color: 'yellow' },
            { name: 'honda crv', latitude: 5.364, longitude: -4.013, color: 'purple' }
        ];

        // Ajout des marqueurs à la carte
        vehicles.forEach(vehicle => {
            L.marker([vehicle.latitude, vehicle.longitude], { icon: L.divIcon({ className: 'my-icon', html: '<div style="background-color: ' + vehicle.color + '; width: 25px; height: 25px; border-radius: 50%;"></div>' }) })
                .addTo(map)
                .bindPopup("<b>" + vehicle.name + "</b>")
                .openPopup();
        });
    </script>
</body>
</html>
