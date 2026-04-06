CHACHA V5.4 — FRONT LIT LES PRODUITS DIRECTEMENT DEPUIS data/products.json

Problème réel probable :
- le front dépendait encore d'un fichier JS statique / cache navigateur
- même si l'admin enregistrait bien les produits, le catalogue pouvait afficher une ancienne version

Correctif définitif :
- includes/store_footer.php injecte maintenant directement window.CHACHA_PRODUCTS depuis data/products.json via PHP
- le front ne dépend plus du fichier assets/js/products.js pour afficher les produits
- plus de souci de cache sur le catalogue / shop / client

Résultat :
- tout produit ajouté / modifié / supprimé en admin apparaît immédiatement côté front
