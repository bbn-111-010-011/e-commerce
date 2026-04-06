CHACHA V7.1.5 — CORRECTIF CATÉGORIES DISPARUES

Cause probable corrigée :
- certaines catégories en base n'avaient pas exactement les bons slugs attendus par le front
- le front utilisait des filtres trop rigides

Correctifs appliqués :
- products-api.php normalise maintenant category et categoryLabel
- catalogue.php génère les filtres dynamiquement à partir des produits réellement trouvés
- catalogue.js devient tolérant aux variations de catégories
- home.js devient plus robuste aussi

Résultat attendu :
- les autres catégories réapparaissent côté client
- les filtres du catalogue suivent les vraies catégories disponibles
