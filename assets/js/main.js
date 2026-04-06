async function loadProducts() {
  try {
    const res = await fetch(`products-api.php?t=${Date.now()}`, { cache: 'no-store' });
    if (res.ok) {
      const data = await res.json();
      if (Array.isArray(data)) {
        window.CHACHA_PRODUCTS = data;
        return data;
      }
    }
  } catch (e) {}

  return Array.isArray(window.CHACHA_PRODUCTS) ? window.CHACHA_PRODUCTS : [];
}

function money(v){ return `${Number(v).toFixed(2)} €`; }
window.Chacha={loadProducts,money};
