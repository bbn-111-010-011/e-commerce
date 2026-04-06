CHACHA V4.2 — CORRECTIF ADMIN PROPRE

Contenu :
- includes/auth.php corrigé et complet
- redirect_if_logged_in() rétabli
- redirection admin directe vers admin/dashboard.php après connexion
- blocage du front pour l'admin (store_header + account)
- create_admin_once.php fourni pour créer ou remettre à zéro un admin

Utilisation :
1. remplacez les fichiers du projet par cette version
2. ouvrez create_admin_once.php si besoin pour créer l'admin
3. notez l'email / mot de passe affichés
4. supprimez create_admin_once.php après usage
5. ouvrez logout.php
6. reconnectez-vous : l'admin ira directement sur admin/dashboard.php
