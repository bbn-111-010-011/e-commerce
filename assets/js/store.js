function chachaLoginRedirect(target = '') {
  const redirect = target && target.trim() !== '' ? target : 'panier.php';
  window.location.href = `login.php?redirect=${encodeURIComponent(redirect)}`;
}

function updateCartCountUi(count) {
  document.querySelectorAll('[data-cart-count]').forEach(el => el.textContent = count);
}

async function addToCartServer(productId, qty = 1, size = 'M') {
  if (!window.CHACHA_IS_LOGGED_IN) {
    chachaLoginRedirect('panier.php');
    return false;
  }

  const body = new URLSearchParams({
    product_id: String(productId),
    qty: String(qty),
    size: String(size),
    ajax: '1'
  });

  const res = await fetch('actions/add_to_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body,
    credentials: 'same-origin'
  });

  const data = await res.json();

  if (data.redirect) {
    window.location.href = data.redirect;
    return false;
  }

  if (data.success) {
    updateCartCountUi(data.cart_count || 0);
    alert(data.message || 'Produit ajouté au panier');
    return true;
  }

  alert(data.message || 'Erreur panier');
  return false;
}

async function toggleFavoriteServer(productId) {
  if (!window.CHACHA_IS_LOGGED_IN) {
    chachaLoginRedirect('favorites.php');
    return false;
  }

  const body = new URLSearchParams({
    product_id: String(productId),
    ajax: '1'
  });

  const res = await fetch('actions/toggle_favorite.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body,
    credentials: 'same-origin'
  });

  const data = await res.json();

  if (data.redirect) {
    window.location.href = data.redirect;
    return false;
  }

  if (!data.success) {
    alert(data.message || 'Erreur favoris');
    return false;
  }

  window.CHACHA_FAVORITES = data.favorite_ids || [];
  document.querySelectorAll(`[data-favorite-id="${productId}"]`).forEach(el => {
    el.textContent = data.is_favorite ? 'Retirer des favoris' : 'Ajouter aux favoris';
  });

  if (data.refresh) {
    window.location.reload();
  } else {
    alert(data.message || 'Favoris mis à jour');
  }
  return true;
}

window.addToCartServer = addToCartServer;
window.toggleFavoriteServer = toggleFavoriteServer;
