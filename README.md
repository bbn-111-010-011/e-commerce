# CHACHA — Boutique e-commerce PHP / MySQL

## Présentation du projet

**CHACHA** est une boutique e-commerce développée en **PHP natif + MySQL**, conçue pour vendre des vêtements traditionnels et élégants (caftans, karakou, robes de soirée, etc.).

Le projet a été pensé pour être :
- **simple à héberger**
- **facile à maintenir**
- **compatible avec un hébergement mutualisé** (ex : **Hostinger**)
- **sans framework**, pour rester clair et accessible

Cette version repose sur une **base stable** du projet, préparée pour :
- la **vitrine client**
- la **gestion du panier**
- les **favoris**
- la **connexion / inscription**
- la **passation de commande**
- le **suivi des commandes**
- un **espace administrateur**

---

# Fonctionnalités principales

## Côté client
- Page d’accueil / vitrine
- Catalogue produits
- Fiches produits
- Connexion / inscription client
- Ajout au panier
- Gestion du panier
- Ajout aux favoris
- Gestion des favoris
- Validation de commande
- Espace client
- Historique des commandes
- Suivi du statut des commandes

## Côté administrateur
- Connexion administrateur
- Dashboard admin
- Gestion des commandes
- Modification du statut des commandes
- Gestion des produits (selon la version stable utilisée)
- Consultation des données clients / commandes

---

# Stack technique

- **PHP 8+**
- **MySQL / MariaDB**
- **PDO** (connexion sécurisée)
- **HTML / CSS / JS**
- **Sessions PHP**
- **Protection CSRF**
- **Architecture modulaire simple**

---

# Structure du projet

```bash
CHACHA/
│
├── actions/                # Traitements POST (login, register, panier, commande, etc.)
├── admin/                  # Espace administrateur
├── config/                 # Configuration BDD
├── data/                   # Données / fichiers internes (si utilisés)
├── docs/                   # Documentation projet
├── includes/               # Helpers, auth, session, csrf, bootstrap boutique
├── sql/                    # Scripts SQL d’installation / migration
│
├── index.php               # Point d’entrée principal
├── shop.php                # Page boutique / vitrine
├── catalogue.php           # Catalogue produits
├── product.php             # Fiche produit
├── cart.php                # Panier
├── favorites.php           # Favoris
├── checkout.php            # Validation commande
├── order-success.php       # Confirmation de commande
├── account.php             # Espace client
├── login.php               # Connexion client
├── register.php            # Inscription client
├── logout.php              # Déconnexion
│
└── .htaccess               # Configuration Apache / Hostinger

