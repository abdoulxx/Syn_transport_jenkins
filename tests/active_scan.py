#!/usr/bin/env python
from zapv2 import ZAPv2

# URL cible
target = 'https://regisono.com'
# Clé API pour ZAP (si activée)
apiKey = '1odfud9vtbks0u32430lmt6cqc'

# Initialisation de ZAP
zap = ZAPv2(apikey=apiKey, proxies={'http': 'http://127.0.0.1:8081', 'https': 'http://127.0.0.1:8081'})

# Pagination des alertes
st = 0  # Début
pg = 5000  # Nombre d'alertes à récupérer par page
alert_count = 0  # Compteur d'alertes
blacklist = [1, 2]  # Liste des alertes à ignorer

# Récupération des alertes
alerts = zap.alert.alerts(baseurl=target, start=st, count=pg)

print("\n===== SCAN DE SÉCURITÉ ZAP =====")
print(f"URL analysée : {target}\n")

while len(alerts) > 0:
    print(f"Lecture de {len(alerts)} alertes à partir de {st}...\n")
    alert_count += len(alerts)

    for alert in alerts:
        plugin_id = alert.get('pluginId')
        if plugin_id in blacklist:
            continue

        # Récupération des détails de l'alerte
        nom_alerte = alert.get('name', 'Inconnu')
        risque = alert.get('risk', 'Non évalué')
        url = alert.get('url', 'Non spécifiée')
        param = alert.get('param', 'Aucun')
        preuve = alert.get('evidence', 'Non disponible')

        # Affichage formaté
        print(" Alerte détectée !")
        print(f"    Nom : {nom_alerte}")
        print(f"    Niveau de risque : {risque}")
        print(f"    URL concernée : {url}")
        print(f"    Paramètre affecté : {param}")
        print(f"    Preuve : {preuve}\n")
        print("-" * 50)

    # Passage à la page suivante
    st += pg
    alerts = zap.alert.alerts(baseurl=target, start=st, count=pg)

print(f"\nTotal des alertes détectées : {alert_count}")