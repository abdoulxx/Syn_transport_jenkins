#!/usr/bin/env python3
import time
from zapv2 import ZAPv2

# URL de l'application cible
target = 'https://regisono.com'
# Clé API de ZAP (à modifier selon ta configuration)
apiKey = '1odfud9vtbks0u32430lmt6cqc'

# Connexion à l'API OWASP ZAP avec le bon port
zap = ZAPv2(apikey=apiKey, proxies={'http': 'http://127.0.0.1:8081', 'https': 'http://127.0.0.1:8081'})

# Vérifier la connexion
try:
    print(f"Connexion à ZAP : Version {zap.core.version}")
except Exception as e:
    print(f" Erreur de connexion à ZAP : {e}")
    exit(1)

# Attendre que ZAP soit prêt
print(" Attente du démarrage de ZAP...")
time.sleep(10)

# Lancer le Spider Scan
print(f" Lancement du Spider sur {target}")
scanID = zap.spider.scan(target)

# Vérifier si le scan a démarré correctement
if scanID.isdigit():
    while int(zap.spider.status(scanID)) < 100:
        print(f" Progression du scan : {zap.spider.status(scanID)}%")
        time.sleep(2)

    print(" Spider terminé avec succès !")
    print("\nURLs découvertes :")
    print('\n'.join(zap.spider.results(scanID)))
else:
    print(" Échec du lancement du Spider. Vérifiez l'API ZAP.")
