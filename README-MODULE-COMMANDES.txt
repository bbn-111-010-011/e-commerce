CHACHA V1 - MODULE COMMANDE COMPLET

Ajouts :
- checkout.php sécurisé
- actions/place_order.php
- order-success.php
- account-orders.php
- order-details.php
- includes/shop_bootstrap.php
- includes/order_helpers.php
- sql/chacha-v1-module-commandes.sql

Fonctionnement :
1. le client ajoute au panier
2. il se connecte
3. il passe par checkout.php
4. la commande est enregistrée dans orders + order_items + payments
5. le panier session est vidé
6. il retrouve ses commandes dans account-orders.php
