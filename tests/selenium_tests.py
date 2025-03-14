from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time
import sys

# Optionnel : forcer l'encodage en UTF-8 pour la sortie standard (si nécessaire)
if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

# Configuration du WebDriver pour Brave
service = Service(r"C:\WebDriver\bin\chromedriver.exe")
options = webdriver.ChromeOptions()

# Indiquer le chemin du profil Chrome (répertoire de profil existant)
profile_path = r"C:\Users\aboul\AppData\Local\Google\Chrome\User Data"  # Chemin du profil utilisateur de Chrome

# Utiliser le profil existant de Chrome
options.add_argument(f"user-data-dir={profile_path}")
options.add_argument("profile-directory=Profile 1")  # Si vous souhaitez utiliser le profil 'Default'

# Lancer le navigateur
driver = webdriver.Chrome(service=service, options=options)

# Fonction d'inscription (uniquement pour le client)
def s_inscrire(role, nom, email, numero_telephone, password, **kwargs):
    driver.get("https://regisono.com/inscription.php")
    WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.ID, "inscription-form")))

    # Sélection du rôle (Client uniquement)
    select_role = driver.find_element(By.ID, "role")
    select_role.click()
    select_role.find_element(By.XPATH, f".//option[@value='{role}']").click()
    time.sleep(2)  # Attendre la mise à jour du formulaire

    # Remplissage des champs communs
    driver.find_element(By.NAME, "nom").send_keys(nom)
    driver.find_element(By.NAME, "email").send_keys(email)
    driver.find_element(By.NAME, "numero_telephone").send_keys(numero_telephone)
    driver.find_element(By.NAME, "password").send_keys(password)
    driver.find_element(By.NAME, "confirm_password").send_keys(password)

    # Champs spécifiques au client
    if role == "client":
        driver.find_element(By.NAME, "localisation").send_keys(kwargs.get("localisation", "Abidjan"))

    # Soumission du formulaire
    driver.find_element(By.XPATH, "//input[@type='submit' and @value=\"S'inscrire\"]").click()

    # Attendre le résultat (vérification de redirection ou message de succès)
    try:
        WebDriverWait(driver, 10).until(EC.url_changes("https://regisono.com/inscription.php"))
        print(f"Inscription réussie pour {nom} ({role})")
    except:
        print("L'inscription a échoué. Vérifiez les erreurs.")

# Fonction de connexion
def se_connecter(email, mot_de_passe):
    driver.get("https://regisono.com/login.php")
    time.sleep(3)

    champ_email = driver.find_element(By.NAME, "email")
    champ_mdp = driver.find_element(By.NAME, "password")
    champ_email.send_keys(email)
    champ_mdp.send_keys(mot_de_passe)

    bouton_connexion = driver.find_element(By.XPATH, "//input[@type='submit' and @value='Se connecter']")
    bouton_connexion.click()
    time.sleep(3)

# Fonction de test de la page d'accueil
def tester_accueil():
    driver.get("https://regisono.com")
    time.sleep(3)
    assert "REGISONO - Plateforme de services musicaux" in driver.title, "Le titre de la page d'accueil n'est pas correct"
    print("Page d'accueil testée avec succès!")

# Fonction de déconnexion
def se_deconnecter():
    try:
        bouton_deconnexion = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable((By.XPATH, "//a[contains(@href, 'deconnexion.php') and contains(text(), 'Déconnexion')]"))
        )
        bouton_deconnexion.click()
        time.sleep(3)
        print("Déconnexion réussie!")
    except Exception as e:
        print(f"Impossible de se déconnecter. Erreur : {e}")

# Test de Responsive Design
def test_responsive_design():
    resolutions = {
        "Mobile (iPhone 12)": (390, 844),
        "Tablette (iPad)": (768, 1024),
        "Desktop (Full HD)": (1920, 1080)
    }
    
    for device, (width, height) in resolutions.items():
        driver.set_window_size(width, height)
        driver.get("https://regisono.com")
        time.sleep(2)

        try:
            driver.execute_script("""
                return new Promise(resolve => {
                    const scrollStep = 60;
                    const delay = 10;
                    function smoothScroll() {
                        if (window.scrollY + window.innerHeight < document.body.scrollHeight) {
                            window.scrollBy(0, scrollStep);
                            setTimeout(smoothScroll, delay);
                        } else {
                            resolve(true);
                        }
                    }
                    smoothScroll();
                });
            """)
            WebDriverWait(driver, 10).until(
                lambda d: d.execute_script("return window.scrollY + window.innerHeight >= document.body.scrollHeight")
            )
            assert "REGISONO" in driver.title
            print(f"Responsive test réussi pour {device} ({width}x{height})")
        except Exception as e:
            print(f"Le site ne s'affiche pas correctement sur {device} ({width}x{height}) - Erreur : {e}")

# Test des Liens de Navigation
def test_liens_navigation():
    driver.get("https://regisono.com")
    WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.TAG_NAME, "nav")))
    liens_testes = set()
    liens = driver.find_elements(By.TAG_NAME, "a")
    for index in range(len(liens)):
        try:
            liens = driver.find_elements(By.TAG_NAME, "a")
            lien = liens[index]
            href = lien.get_attribute("href")
            if href and href.startswith("https://regisono.com") and href not in liens_testes:
                liens_testes.add(href)
                lien.click()
                WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.TAG_NAME, "body")))
                print(f"Lien fonctionnel : {href}")
                driver.back()
                WebDriverWait(driver, 5).until(EC.presence_of_element_located((By.TAG_NAME, "nav")))
        except Exception as e:
            print(f"Erreur sur le lien : {href} - Détails : {e}")

# Fonction principale pour exécuter tous les tests
def automatiser_tests():
    tester_accueil()
    s_inscrire(
        role="client",
        nom="Test Client",
        email="testclient@edamd2e.com",
        numero_telephone="0709728025",
        password="Test1234",
        localisation="Cocody"
    )
    se_connecter("testclient@edamd2e.com", "Test1234")
    se_deconnecter()
    test_responsive_design()
    test_liens_navigation()

# Lancer tous les tests
automatiser_tests()

# Fermer le navigateur
driver.quit()
