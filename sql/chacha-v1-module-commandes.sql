-- Chacha V1 - Module commandes complet
USE boutique_chacha;

-- Ajout d'une colonne shipping_amount si elle n'existe pas déjà
-- Si votre table orders existe déjà avec shipping_amount, ignorez cette ligne en cas d'erreur
ALTER TABLE orders
ADD COLUMN shipping_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER subtotal;

-- Page checkout / commande : on utilise principalement ces tables :
-- orders
-- order_items

-- Aucun DROP volontaire pour ne pas casser vos données existantes.
