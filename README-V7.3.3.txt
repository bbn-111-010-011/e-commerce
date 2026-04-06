CHACHA V7.3.3 — MULTI-TAILLES SUR UNE MÊME FICHE PRODUIT

Nouveauté :
- sur une fiche produit, le client peut maintenant choisir plusieurs quantités réparties par taille
- exemple : 1 en S + 1 en L pour la même robe

Correctifs :
- produit.php : nouvelle interface multi-tailles
- assets/js/product.js : gestion des quantités par taille
- actions/add_to_cart.php : accepte size_qty[Taille] = quantité
- contrôle du stock total conservé

Compatibilité conservée :
- CRUD VALIDER
- vitrine
- panier
- favoris
- catégories
