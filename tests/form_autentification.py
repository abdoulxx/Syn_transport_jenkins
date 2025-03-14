import urllib.parse
import json
import time
from zapv2 import ZAPv2

# 🔹 Configuration de l'API ZAP
context_id = 1
apikey = '1odfud9vtbks0u32430lmt6cqc'
context_name = 'Default Context'
target_url = 'https://regisono.com'
json_output_file = 'zap_alerts.json'

# 🔹 Initialisation de ZAP
zap = ZAPv2(apikey=apikey, proxies={'http': 'http://127.0.0.1:8081', 'https': 'http://127.0.0.1:8081'})

# 🔹 Inclusion et exclusion des URL
def set_include_in_context():
    exclude_url = 'https://regisono.com/deconnexion.php'
    include_url = 'https://regisono.com/.*'  
    
    zap.context.include_in_context(context_name, include_url)
    zap.context.exclude_from_context(context_name, exclude_url)
    
    print(' Contexte configuré : Inclusion et exclusion des URL')


# 🔹 Indicateur de connexion réussie
def set_logged_in_indicator():
    logged_in_regex = r'<a href="deconnexion.php">deconnexion</a>'
    zap.authentication.set_logged_in_indicator(context_id, logged_in_regex)
    print(' Configured logged in indicator regex')

# 🔹 Configuration de l'authentification par formulaire
def set_form_based_auth():
    login_url = 'https://regisono.com/login.php'
    login_request_data = 'username={%username%}&password={%password%}'
    form_based_config = 'loginUrl=' + urllib.parse.quote(login_url) + '&loginRequestData=' + urllib.parse.quote(login_request_data)
    zap.authentication.set_authentication_method(context_id, 'formBasedAuthentication', form_based_config)
    print(' Configured form-based authentication')

# 🔹 Création d'un utilisateur de test
def set_user_auth_config():
    user = 'Test Client'
    username = 'testclient@edamd2e.com'
    password = 'Test1234'

    user_id = zap.users.new_user(context_id, user)
    user_auth_config = 'username=' + urllib.parse.quote(username) + '&password=' + urllib.parse.quote(password)
    zap.users.set_authentication_credentials(context_id, user_id, user_auth_config)
    zap.users.set_user_enabled(context_id, user_id, 'true')
    zap.forcedUser.set_forced_user(context_id, user_id)
    zap.forcedUser.set_forced_user_mode_enabled('true')
    print('User authentication configured')
    return user_id

# 🔹 Lancement de l'exploration (Spider)
def start_spider(user_id):
    zap.spider.scan_as_user(context_id, user_id, target_url, recurse='true')
    print(' Started scanning with authentication...')

# 🔹 Attente de la fin du scan
def wait_for_spider():
    while int(zap.spider.status()) < 100:
        print(f" Exploration en cours... {zap.spider.status()}%")
        time.sleep(5)
    print(" Exploration terminée !")

# 🔹 Récupération et affichage des alertes
def get_alerts():
    print("\n Récupération des alertes détectées...\n")
    alerts = zap.alert.alerts(baseurl=target_url, start=0, count=1000)

    if not alerts:
        print(" Aucune alerte détectée.")
    else:
        print(f"{len(alerts)} alertes détectées !")

    return alerts

# 🔹 Exportation des alertes en JSON
def export_alerts_to_json(alerts):
    with open(json_output_file, 'w', encoding='utf-8') as f:
        json.dump(alerts, f, indent=4, ensure_ascii=False)
    print(f" Alertes exportées avec succès dans '{json_output_file}'")

# 🔹 Exécution du scan et export des alertes
set_include_in_context()
set_form_based_auth()
set_logged_in_indicator()
user_id_response = set_user_auth_config()
start_spider(user_id_response)
wait_for_spider()
alerts = get_alerts()
export_alerts_to_json(alerts)
