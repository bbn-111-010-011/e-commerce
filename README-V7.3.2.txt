CHACHA V7.3.2 — Correctif get_flash undefined

Cause: plusieurs pages appelaient get_flash() avant de charger includes/auth.php.
Correctif: chargement de includes/auth.php avant tout appel à get_flash().
produit.php charge aussi includes/shop_bootstrap.php pour la fiche produit.
