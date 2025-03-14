from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
import time
import sys

# Forcer l'encodage en UTF-8 pour éviter les erreurs d'affichage
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

# ✅ Configuration du WebDriver avec WebDriver Manager (évite d'avoir à installer chromedriver manuellement)
options = Options()
options.add_argument("--headless")  # Mode sans interface graphique (désactive si tu veux voir le test en live)
options.add_argument("--no-sandbox")
options.add_argument("--disable-dev-shm-usage")

service = Service(ChromeDriverManager().install())
driver = webdriver.Chrome(service=service, options=options)

# ✅ Fonction de connexion
def se_connecter(email, mot_de_passe):
    try:
        driver.get("https://regisono.com/login.php")
        WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.NAME, "email")))

        driver.find_element(By.NAME, "email").send_keys(email)
        driver.find_element(By.NAME, "password").send_keys(mot_de_passe)
        driver.find_element(By.XPATH, "//input[@type='submit' and @value='Se connecter']").click()

        WebDriverWait(driver, 10).until(EC.url_contains("dashboard") or EC.url_contains("home"))
        print("✅ Connexion réussie !")
    except Exception as e:
        print(f"❌ Erreur lors de la connexion : {e}")

# ✅ Fonction pour commander le Pack Or
def commander_pack_or():
    try:
        driver.get("https://regisono.com")

        WebDriverWait(driver, 15).until(
            EC.presence_of_element_located((By.XPATH, "//h2[@class='section-title']/span[@id='highlight-packs']"))
        )
        print("✅ La page des packs est bien chargée !")

        bouton_commander = WebDriverWait(driver, 15).until(
            EC.element_to_be_clickable((By.XPATH, "//h3[contains(text(),'Pack Or')]/following::button[1]"))
        )

        driver.execute_script("arguments[0].scrollIntoView({behavior: 'smooth', block: 'center'});", bouton_commander)
        bouton_commander.click()
        
        WebDriverWait(driver, 10).until(EC.url_changes(driver.current_url))
        print("✅ Pack Or sélectionné avec succès !")

    except Exception as e:
        print(f"❌ Erreur lors de la sélection du Pack Or : {e}")

# ✅ Fonction pour remplir le formulaire de récapitulatif
def remplir_recapitulatif(localisation, date_livraison):
    try:
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.XPATH, "//h2[contains(text(),'Récapitulatif')]"))
        )
        print("✅ Page récapitulatif chargée !")

        champ_localisation = driver.find_element(By.NAME, "localisation")
        champ_localisation.clear()
        champ_localisation.send_keys(localisation)

        champ_date = driver.find_element(By.NAME, "date_livraison")
        driver.execute_script("arguments[0].value = arguments[1];", champ_date, date_livraison)

        bouton_confirmer = driver.find_element(By.XPATH, "//button[contains(text(),'Confirmer la commande')]")
        bouton_confirmer.click()

        WebDriverWait(driver, 10).until(EC.url_changes(driver.current_url))
        print("✅ Commande confirmée avec date de livraison !")

    except Exception as e:
        print(f"❌ Erreur sur la page de récapitulatif : {e}")

# ✅ Fonction pour finaliser la commande et voir les commandes
def finaliser_commande():
    try:
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.XPATH, "//h1[contains(text(),'Félicitations')]"))
        )
        print("✅ Page de confirmation chargée !")

        bouton_voir_commandes = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.XPATH, "//a[contains(text(),'Voir mes commandes')]"))
        )

        driver.execute_script("arguments[0].scrollIntoView({behavior: 'smooth', block: 'center'});", bouton_voir_commandes)
        bouton_voir_commandes.click()

        WebDriverWait(driver, 10).until(EC.url_contains("mes_commandes"))
        print("✅ Redirection vers la liste des commandes réussie !")

    except Exception as e:
        print(f"❌ Erreur lors de la finalisation de la commande : {e}")

# ✅ Exécution du script
se_connecter("testclient@edamd2e.com", "Test1234")
time.sleep(2)

commander_pack_or()
time.sleep(2)

remplir_recapitulatif("Marcory", "2025-02-10")
time.sleep(2)

finaliser_commande()

# ✅ Fermer le navigateur proprement
driver.quit()
