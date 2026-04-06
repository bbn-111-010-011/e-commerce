CHACHA V6.2 — CORRECTIF PRODUITS FRONT + AJOUT IMAGES

Problème corrigé :
- les nouveaux produits créés en admin n'apparaissaient pas côté client/catalogue

Correctif appliqué :
- nouveau fichier products-api.php
- le front charge désormais toujours les produits directement depuis data/products.json via products-api.php
- cache navigateur contourné avec paramètre timestamp et headers no-cache

Images ajoutées dans assets/img/products :
- caftan-enfant.jpg
- caftan-vert.jpg
- karakou-velours.jpg
- robe-soiree.jpg
- logo.png
