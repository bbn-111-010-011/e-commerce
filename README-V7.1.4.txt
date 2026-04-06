CHACHA V7.1.4 — CORRECTIF GLOBAL APRÈS ANALYSE

Constat :
- le SQL précédent ne passait pas correctement sur votre environnement MySQL/WAMP
- la mise à niveau de la table products devait être rendue plus compatible

Correctifs appliqués :
- nouveau SQL : sql/chacha-v7-1-4-upgrade-products-safe.sql
- sync_products_once.php renforcé
- mêmes images conservées dans assets/img/products

Ordre exact :
1. Importer sql/chacha-v7-1-4-upgrade-products-safe.sql
2. Ouvrir sync_products_once.php
3. Vérifier les tables products et product_sizes
4. Supprimer sync_products_once.php
