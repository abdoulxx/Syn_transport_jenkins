from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

service = Service(r"C:\WebDriver\bin\chromedriver.exe")
options = webdriver.ChromeOptions()
options.add_argument("--incognito")
driver = webdriver.Chrome(service=service, options=options)

# Connexion
def se_connecter(email, mot_de_passe):
    driver.get("https://syn-transport.com/connexion.php")

    WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.TAG_NAME, "form")))
    driver.find_element(By.NAME, "email").send_keys(email)
    driver.find_element(By.NAME, "password").send_keys(mot_de_passe)

    bouton_connexion = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Se connecter')]"))
    )
    bouton_connexion.click()

    WebDriverWait(driver, 10).until(EC.url_changes("https://syn-transport.com/connexion.php"))

# Sélection véhicule
def selectionner_vehicule():
    driver.get("https://syn-transport.com/louer.php")

    premier_vehicule = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable((By.XPATH, "//button[contains(text(), 'Reserver')]"))
    )
    driver.execute_script("arguments[0].scrollIntoView();", premier_vehicule)
    time.sleep(1)
    premier_vehicule.click()

    WebDriverWait(driver, 10).until(EC.url_contains("recap.php?id="))

# Formulaire réservation avec gestion annulation et validation finale
def reservation_avec_annulation(date_debut, date_fin):
    def remplir_et_reserver(paiement_methode):
        WebDriverWait(driver, 10).until(
            EC.presence_of_element_located((By.ID, "reservationForm"))
        )

        # Remplissage dates
        driver.execute_script("""
            document.getElementById('start-date').value = arguments[0];
            document.getElementById('end-date').value = arguments[1];
            calculateTotal();
        """, date_debut, date_fin)

        time.sleep(2)

        # Choix méthode de paiement
        paiement = driver.find_element(By.NAME, "payment_method")
        for option in paiement.find_elements(By.TAG_NAME, "option"):
            if option.text.strip() == paiement_methode:
                option.click()
                break

        bouton_reserver = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.XPATH, "//button[contains(text(),'Réserver')]"))
        )
        driver.execute_script("arguments[0].scrollIntoView();", bouton_reserver)
        bouton_reserver.click()

    # Première tentative avec Mobile Money
    remplir_et_reserver("Mobile Money")

    # Redirection vers paiement externe PayTech
    WebDriverWait(driver, 15).until(EC.url_contains("paytech.sn/payment/checkout"))

    # Simulation annulation
    driver.get("https://www.syn-transport.com/cancel.php")

    # Retour à la page de location pour refaire la réservation
    selectionner_vehicule()

    # Deuxième tentative avec Paiement à la livraison
    remplir_et_reserver("Paiement à la livraison")

    # Attendre confirmation
    WebDriverWait(driver, 10).until(EC.url_changes("recap.php"))

# Lancer le test
se_connecter("testuser@syn-transport.com", "Test1234")
time.sleep(2)

selectionner_vehicule()
time.sleep(2)

reservation_avec_annulation("2025-04-10", "2025-04-15")
time.sleep(2)

driver.quit()