-- CHACHA V6.1
-- Vérifications / ajustements éventuels selon votre base

USE boutique_chacha;

-- Cette version utilise les tables existantes :
-- users
-- user_addresses
-- orders
-- order_items
-- payments

-- Colonnes utilisées côté commande :
-- orders.customer_email
-- orders.customer_phone
-- orders.billing_address_id
-- orders.shipping_address_id
-- orders.shipping_method
-- orders.payment_method
-- orders.notes
-- orders.subtotal
-- orders.shipping_amount
-- orders.total_amount
-- orders.payment_status
-- orders.order_status

-- Aucun DROP volontaire.
