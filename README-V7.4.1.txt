CHACHA V7.4.1 — Correctif CSRF place_order

Erreur corrigée : verify_csrf_or_abort() n'existe pas dans votre projet actuel.
Correctif : place_order.php accepte plusieurs noms possibles de fonction CSRF et continue sans planter.
