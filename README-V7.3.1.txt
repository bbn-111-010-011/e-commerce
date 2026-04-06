CHACHA V7.3.1 — Correctif include header/footer

Erreur corrigée : shop.php cherchait includes/store_header.php alors que le fichier n'existait pas dans certaines versions.
Correctif appliqué :
- ajout de wrappers de compatibilité store_header.php / store_footer.php si nécessaire
- correction des pages pour pointer vers les bons includes
