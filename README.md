# Module PHP connexion / inscription — Chacha

## Contenu
- `register.php` : création de compte client
- `login.php` : connexion client
- `logout.php` : déconnexion
- `account.php` : page protégée
- `config/database.php` : connexion PDO MySQL
- `includes/auth.php` : helpers d'authentification
- `includes/session.php` : session sécurisée
- `includes/csrf.php` : protection CSRF
- `actions/register_action.php` : traitement inscription
- `actions/login_action.php` : traitement connexion

## Base utilisée
Compatible avec la table `users` déjà prévue dans `boutique_chacha`.

## Champs attendus dans `users`
- `id`
- `first_name`
- `last_name`
- `email`
- `phone`
- `password_hash`
- `role`
- `is_active`

## Installation
1. Copiez le dossier dans votre projet PHP.
2. Vérifiez les accès MySQL dans `config/database.php`.
3. Assurez-vous que la table `users` existe.
4. Ouvrez `register.php` ou `login.php`.

## Suite logique
- brancher le panier à la session client
- ajouter mot de passe oublié
- ajouter modification de profil
- ajouter historique commandes
