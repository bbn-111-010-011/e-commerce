CHACHA V5.3 — SYNCHRONISATION PRODUITS FRONT/ADMIN

Problème corrigé :
- les nouveaux produits ajoutés en admin étaient enregistrés dans data/products.json
- mais le front catalogue utilisait encore assets/js/products.js
- donc le catalogue client ne voyait pas les nouveaux produits

Correctif appliqué :
- chaque sauvegarde produit met maintenant à jour :
  1. data/products.json
  2. assets/js/products.js

Résultat :
- les produits créés / modifiés / supprimés en admin apparaissent bien côté catalogue/front
