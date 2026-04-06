CHACHA V5 — CRUD PRODUITS ADMIN COMPLET

Ajouts :
- admin/products.php : liste admin avec image + actions
- admin/product-add.php : ajout produit
- admin/product-edit.php : modification produit
- admin/product-save.php : sauvegarde ajout / édition
- admin/product-delete.php : suppression produit
- includes/product_admin_helpers.php : helpers CRUD JSON
- upload image produit vers assets/img/products/

Fonctionnement :
- les produits sont gérés dans data/products.json
- chaque ajout / modification met à jour products.json
- les images uploadées sont copiées dans assets/img/products/
- les catégories restent compatibles avec le front actuel

À faire :
1. remplacez votre dossier projet actif par cette version
2. connectez-vous en admin
3. ouvrez admin/products.php
4. testez ajout / modification / suppression
