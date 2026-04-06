CHACHA V7.4.2 — Correctif schéma orders

Erreur corrigée : la colonne shipping_status n'existe pas dans votre table orders.
Correctif : place_order.php détecte maintenant les colonnes réellement présentes dans orders, order_addresses et order_items avant insertion.
La commande s'adapte donc à votre schéma actuel sans planter sur une colonne absente.
