CHACHA V7.3 — FICHE PRODUIT + STOCK + VARIANTES

Base utilisée : checkpoint CRUD VALIDER (vitrine + CRUD + panier + favoris conservés)

Cette version ajoute :
- fiche produit enrichie
- galerie image si product_images existe, sinon image principale
- affichage du SKU si disponible
- affichage du statut de stock : en stock / stock faible / rupture
- sélection obligatoire de la taille avant ajout panier
- quantité limitée au stock disponible
- validation serveur du stock et de la taille sur add_to_cart et update_cart
- compatibilité maintenue avec vitrine, panier, favoris et catégories existantes

Fichiers principaux modifiés :
- produit.php
- products-api.php
- includes/shop_bootstrap.php
- actions/add_to_cart.php
- actions/update_cart.php
- assets/js/product.js
- assets/css/styles.css
