-- CHACHA V4 - Ajustements admin + espace client
USE boutique_chacha;

-- Cette table existe normalement déjà, mais voici un rappel des rôles attendus :
-- users.role = 'admin' ou 'client'

-- Colonnes utiles possibles pour la gestion admin / client
-- À exécuter seulement si nécessaire selon votre base :

-- ALTER TABLE orders ADD COLUMN admin_comment TEXT NULL AFTER notes;
-- ALTER TABLE orders ADD COLUMN updated_by_admin INT UNSIGNED NULL AFTER admin_comment;

-- Pas de DROP ici pour éviter toute perte de données.
