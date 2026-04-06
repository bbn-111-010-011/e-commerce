Cette version corrige l'affichage local.
Le problème venait du chargement des produits via fetch(data/products.json), souvent bloqué en ouvrant directement index.html.
Ici, les produits sont injectés via assets/js/products.js, donc le site fonctionne même sans serveur local.
