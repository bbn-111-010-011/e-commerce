CHACHA V7.1 — MIGRATION PRODUITS VERS MYSQL

Objectif :
- faire de MySQL la source unique de vérité pour les produits
- admin CRUD produits écrit en base
- front catalogue lit les produits depuis MySQL
- fin du problème : produit visible côté client mais absent de la BDD

Ce pack ajoute :
- sql/chacha-v7-1-migration-produits-mysql.sql
- includes/product_admin_helpers.php (version MySQL)
- products-api.php (lecture depuis MySQL)
- admin/products.php
- admin/product-add.php
- admin/product-edit.php
- admin/product-save.php
- admin/product-delete.php
- sync_products_once.php (import initial depuis data/products.json)

Étapes conseillées :
1. Importer sql/chacha-v7-1-migration-produits-mysql.sql dans phpMyAdmin
2. Ouvrir sync_products_once.php pour importer les produits déjà présents dans data/products.json
3. Supprimer sync_products_once.php après usage
4. Tester admin/products.php
5. Ajouter un nouveau produit en admin
6. Vérifier :
   - visible côté client
   - visible dans la table products
   - visible dans la table product_sizes

Images fournies incluses dans assets/img/products :
- caftan-enfant.jpg
- caftan-vert.jpg
- karakou-velours.jpg
- robe-soiree.jpg
- logo.png
