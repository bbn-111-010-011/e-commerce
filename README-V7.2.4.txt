CHACHA V7.2.4 — CRUD ADMIN PRODUITS = BDD

Objectif :
- quand l'admin ajoute un produit, il est inséré en BDD
- quand l'admin modifie un produit, il est mis à jour en BDD
- quand l'admin supprime un produit, il est supprimé de la BDD

Fichiers principaux :
- includes/product_admin_helpers.php
- admin/products.php
- admin/product-save.php
- admin/product-delete.php

Test :
1. ajouter un produit depuis admin/product-add.php
2. vérifier la ligne dans products
3. modifier le produit
4. vérifier la mise à jour en BDD
5. supprimer le produit
6. vérifier qu'il disparaît de products et de la vitrine
