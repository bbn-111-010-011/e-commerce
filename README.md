🏋️‍♂️ SPORT PLUS — Boutique e-commerce Sportwear PHP / MySQL

🚀 SPORT PLUS est une boutique e-commerce développée en PHP natif + MySQL, spécialisée dans la vente de vêtements, chaussures et accessoires de sport.
Le projet a été conçu pour être moderne, léger, facile à maintenir et compatible avec un hébergement mutualisé comme Hostinger.

📌 Sommaire
📖 Présentation du projet
🎯 Objectifs du projet
📝 Cahier des charges
✨ Fonctionnalités principales
🛠️ Stack technique
📂 Structure du projet
⚙️ Installation locale
🌍 Déploiement sur Hostinger
🔐 Sécurité minimale intégrée
📈 Évolutions possibles
👨‍💻 Auteur / Utilisation
📖 Présentation du projet

SPORT PLUS est un site e-commerce complet permettant de :

présenter une boutique sportwear moderne
afficher un catalogue produits
gérer un panier
gérer des favoris
permettre la connexion / inscription
enregistrer des commandes
offrir un suivi client
proposer un espace administrateur

🎯 L’objectif est de fournir une base stable, professionnelle et facilement déployable pour :

un portfolio
une présentation de projet
un projet scolaire
une mise en ligne réelle
🎯 Objectifs du projet

Le projet SPORT PLUS a été pensé pour répondre à plusieurs objectifs :

Côté utilisateur 👤
Offrir une expérience d’achat simple et fluide
Permettre de consulter les produits facilement
Commander rapidement depuis mobile ou PC
Avoir un site moderne et responsive
Côté administrateur 🛠️
Gérer les produits
Gérer les commandes
Mettre à jour les statuts
Suivre l’activité de la boutique
Disposer d’une base claire et facile à maintenir
📝 Cahier des charges
📍 Contexte

Le projet consiste à développer une boutique e-commerce sportwear permettant la vente en ligne de produits liés au sport et au fitness.

🎯 Besoin principal

Créer un site web capable de :

mettre en valeur les produits
permettre aux clients de créer un compte
ajouter des produits au panier
enregistrer des commandes
suivre les commandes côté client
administrer les produits et commandes côté back-office
📌 Contraintes techniques
Développement en PHP natif
Utilisation de MySQL / MariaDB
Compatibilité avec Hostinger
Pas de framework lourd
Architecture simple et modulaire
Sécurisation minimale des formulaires
Interface responsive
🎨 Cible visuelle

Le design doit être :

moderne
sportif
dynamique
clair
orienté conversion
adapté mobile / desktop
✨ Fonctionnalités principales
🛍️ Côté client
🏠 Page d’accueil / vitrine
🧥 Catalogue produits sportwear
🔍 Fiches produits détaillées
❤️ Ajout aux favoris
🛒 Ajout au panier
➕ / ➖ Gestion des quantités dans le panier
🔐 Connexion / inscription client
💳 Validation de commande
📦 Historique des commandes
🚚 Suivi du statut des commandes
👤 Espace client
🛠️ Côté administrateur
🔑 Connexion administrateur
📊 Dashboard admin
📦 Gestion des commandes
🔄 Modification du statut des commandes
👕 Gestion des produits (CRUD)
👥 Consultation des clients / commandes
🖼️ Gestion des images produits (selon version)
🛠️ Stack technique

Le projet repose sur une stack volontairement simple, claire et efficace :

🐘 PHP 8+
🗄️ MySQL / MariaDB
🔐 PDO (requêtes préparées)
🎨 HTML5 / CSS3
⚡ JavaScript
🧠 Sessions PHP
🛡️ Protection CSRF
🌐 Apache / .htaccess
📂 Structure du projet
SPORT-PLUS/
│
├── actions/                # Traitements POST (login, register, panier, commande...)
├── admin/                  # Espace administrateur
├── config/                 # Configuration base de données
├── data/                   # Données internes / fichiers temporaires
├── docs/                   # Documentation du projet
├── includes/               # Helpers, auth, session, csrf, bootstrap boutique
├── sql/                    # Scripts SQL d'installation / migration
│
├── index.php               # Page d'accueil
├── shop.php                # Boutique / vitrine
├── catalogue.php           # Catalogue produits
├── product.php             # Fiche produit
├── cart.php                # Panier
├── favorites.php           # Favoris
├── checkout.php            # Validation commande
├── order-success.php       # Confirmation commande
├── account.php             # Espace client
├── login.php               # Connexion client
├── register.php            # Inscription client
├── logout.php              # Déconnexion
│
└── .htaccess               # Configuration Apache / Hostinger
🧾 Types de produits prévus

SPORT PLUS peut inclure plusieurs catégories de produits :

👕 T-shirts techniques
🩳 Shorts / joggings
🧘 Leggings
🧥 Sweats / vestes
👟 Sneakers / chaussures de sport
🎒 Sacs de sport
🧢 Casquettes / accessoires
🏋️ Accessoires fitness
💧 Gourdes / équipements légers
