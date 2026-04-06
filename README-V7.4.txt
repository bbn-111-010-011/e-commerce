CHACHA V7.4 — PANIER INTELLIGENT + DÉCRÉMENTATION STOCK COMMANDE

Base conservée : CRUD VALIDER + V7.3 stable

Ajouts :
- panier clair avec une ligne par taille
- modification quantité et taille directement dans panier.php
- revalidation stricte du stock au moment de la commande
- décrémentation automatique du stock dans products après validation
- blocage si stock insuffisant entre panier et checkout

Fichiers principaux modifiés :
- actions/place_order.php
- panier.php
- assets/css/styles.css
