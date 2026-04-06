CHACHA V7.2 — CRUD PRODUITS 100% MYSQL STABLE

Objectif atteint :
- produits gérés uniquement en base MySQL
- ajout admin => insertion BDD
- modification admin => update BDD
- suppression admin => suppression BDD
- front catalogue/home lit uniquement MySQL

Fichiers principaux :
- sql/chacha-v7-2-products-mysql-stable.sql
- products-api.php
- includes/product_admin_helpers.php
- admin/products.php
- admin/product-add.php
- admin/product-edit.php
- admin/product-save.php
- admin/product-delete.php
- sync_products_once.php

Ordre conseillé :
1. Importer sql/chacha-v7-2-products-mysql-stable.sql
2. Ouvrir sync_products_once.php une seule fois pour récupérer les anciens articles JSON
3. Supprimer sync_products_once.php
4. Tester ajout / modification / suppression depuis l'admin
5. Vérifier le catalogue client et la table products
