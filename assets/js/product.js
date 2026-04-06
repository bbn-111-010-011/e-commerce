document.addEventListener('DOMContentLoaded', async () => {
  const params = new URLSearchParams(location.search);
  const id = Number(params.get('id') || 1);
  const products = await Chacha.loadProducts();
  const p = products.find(x => Number(x.id) === id) || products[0];

  if (!p) {
    const name = document.getElementById('product-name');
    if (name) name.textContent = 'Produit introuvable';
    return;
  }

  const favoriteIds = window.CHACHA_FAVORITES || [];
  const gallery = Array.isArray(p.images) && p.images.length ? p.images : [p.image];
  const availableSizes = Array.isArray(p.sizes) && p.sizes.length ? p.sizes : ['S','M','L','XL','XXL'];
  const stock = Number(p.stock || 0);
  const perSizeQty = {};
  availableSizes.forEach(size => perSizeQty[size] = 0);

  let selectedImage = gallery[0];

  const stockBadge = document.getElementById('product-stock-badge');
  const stockMessage = document.getElementById('product-stock-message');
  const stockText = document.getElementById('product-stock');
  const addBtn = document.getElementById('addBtn');
  const sizeHelp = document.getElementById('size-help');

  function stockStatusLabel() {
    if (stock <= 0) return { text: 'Rupture de stock', css: 'out' };
    if (stock <= 3) return { text: 'Stock faible', css: 'low' };
    return { text: 'En stock', css: 'in' };
  }

  function totalSelectedQty() {
    return Object.values(perSizeQty).reduce((sum, v) => sum + Number(v || 0), 0);
  }

  function renderGallery() {
    const main = document.getElementById('product-image');
    const thumbs = document.getElementById('product-thumbs');
    main.src = selectedImage;
    main.alt = p.name;
    thumbs.innerHTML = gallery.map((img, index) =>
      `<button class="thumb-btn ${img === selectedImage ? 'active' : ''}" type="button" data-image="${img}">
        <img src="${img}" alt="${p.name} vue ${index + 1}">
      </button>`
    ).join('');

    thumbs.querySelectorAll('[data-image]').forEach(btn => {
      btn.addEventListener('click', () => {
        selectedImage = btn.dataset.image;
        renderGallery();
      });
    });
  }

  function renderMultiSizeGrid() {
    const wrap = document.getElementById('multi-size-grid');
    wrap.innerHTML = availableSizes.map(size => {
      const qty = Number(perSizeQty[size] || 0);
      const disabled = stock <= 0 ? 'disabled' : '';
      return `
        <div class="multi-size-card ${qty > 0 ? 'active' : ''}">
          <div class="multi-size-head">
            <strong>${size}</strong>
            <span class="small muted">${qty > 0 ? qty + ' sélectionné(s)' : '0 sélectionné'}</span>
          </div>
          <div class="qty-inline">
            <button class="qty-btn" type="button" data-action="minus" data-size="${size}" ${disabled}>-</button>
            <input class="input qty-input qty-input-small" type="number" min="0" max="${stock}" value="${qty}" data-size-input="${size}" ${disabled}>
            <button class="qty-btn" type="button" data-action="plus" data-size="${size}" ${disabled}>+</button>
          </div>
        </div>
      `;
    }).join('');

    wrap.querySelectorAll('[data-action]').forEach(btn => {
      btn.addEventListener('click', () => {
        const size = btn.dataset.size;
        const action = btn.dataset.action;
        let current = Number(perSizeQty[size] || 0);
        if (action === 'minus') current = Math.max(0, current - 1);
        if (action === 'plus') current = Math.min(stock, current + 1);
        perSizeQty[size] = current;
        normalizeTotals(size);
        renderMultiSizeGrid();
        renderState();
      });
    });

    wrap.querySelectorAll('[data-size-input]').forEach(input => {
      input.addEventListener('input', () => {
        const size = input.dataset.sizeInput;
        let current = Number(input.value || 0);
        if (Number.isNaN(current)) current = 0;
        current = Math.max(0, Math.min(stock, current));
        perSizeQty[size] = current;
        normalizeTotals(size);
        renderMultiSizeGrid();
        renderState();
      });
    });
  }

  function normalizeTotals(changedSize) {
    let total = totalSelectedQty();
    if (total <= stock) return;
    const overflow = total - stock;
    perSizeQty[changedSize] = Math.max(0, Number(perSizeQty[changedSize] || 0) - overflow);
  }

  function renderState() {
    const status = stockStatusLabel();
    stockBadge.textContent = status.text;
    stockBadge.className = `stock-badge ${status.css}`;

    const totalQty = totalSelectedQty();

    if (stock <= 0) {
      stockMessage.textContent = 'Ce produit est actuellement indisponible.';
      stockText.textContent = '0';
      addBtn.disabled = true;
      addBtn.textContent = 'Rupture de stock';
      sizeHelp.textContent = 'Aucune commande possible pour le moment.';
      return;
    }

    stockText.textContent = String(stock);
    if (stock <= 3) {
      stockMessage.textContent = `Plus que ${stock} en stock.`;
    } else {
      stockMessage.textContent = 'Disponible immédiatement.';
    }

    if (totalQty <= 0) {
      addBtn.disabled = true;
      addBtn.textContent = 'Choisissez vos quantités';
      sizeHelp.textContent = 'Sélectionnez une quantité pour une ou plusieurs tailles.';
    } else {
      addBtn.disabled = false;
      addBtn.textContent = `Ajouter ${totalQty} article(s) au panier`;
      sizeHelp.textContent = `Total sélectionné : ${totalQty} / ${stock} disponible(s).`;
    }
  }

  document.getElementById('product-name').textContent = p.name;
  document.getElementById('product-category').textContent = p.categoryLabel || '';
  document.getElementById('product-category-2').textContent = p.categoryLabel || '';
  document.getElementById('product-price').textContent = Chacha.money(Number(p.price || 0));
  document.getElementById('product-old-price').textContent = Number(p.oldPrice || 0) > 0 ? Chacha.money(Number(p.oldPrice || 0)) : '';
  document.getElementById('product-description').textContent = p.description || '';
  document.getElementById('product-color').textContent = p.color || '—';
  document.getElementById('product-sku').textContent = p.sku && String(p.sku).trim() !== '' ? p.sku : '—';

  const badge = document.getElementById('product-badge');
  badge.textContent = p.badge || '';
  badge.style.display = p.badge ? 'inline-flex' : 'none';

  renderGallery();
  renderMultiSizeGrid();
  renderState();

  const favBtn = document.getElementById('favoriteBtn');
  favBtn.textContent = favoriteIds.includes(Number(p.id)) ? 'Retirer des favoris' : 'Ajouter aux favoris';
  favBtn.setAttribute('data-favorite-id', String(p.id));
  favBtn.addEventListener('click', () => toggleFavoriteServer(p.id));

  addBtn.addEventListener('click', async () => {
    if (stock <= 0) {
      alert('Ce produit est en rupture de stock.');
      return;
    }
    const selections = Object.entries(perSizeQty).filter(([, qty]) => Number(qty) > 0);
    if (!selections.length) {
      alert('Veuillez choisir au moins une quantité.');
      return;
    }

    const form = new FormData();
    form.append('product_id', String(p.id));
    form.append('ajax', '1');
    selections.forEach(([size, qty]) => {
      form.append(`size_qty[${size}]`, String(qty));
    });

    try {
      const res = await fetch('actions/add_to_cart.php', { method: 'POST', body: form });
      const data = await res.json();
      if (data.redirect) {
        location.href = data.redirect;
        return;
      }
      if (!data.success) {
        alert(data.message || 'Erreur lors de l’ajout au panier');
        return;
      }
      alert(data.message || 'Produit ajouté au panier');
      Object.keys(perSizeQty).forEach(size => perSizeQty[size] = 0);
      renderMultiSizeGrid();
      renderState();
    } catch (e) {
      alert('Erreur réseau lors de l’ajout au panier');
    }
  });

  const related = products.filter(x => x.category === p.category && Number(x.id) !== Number(p.id)).slice(0, 4);
  document.getElementById('related-grid').innerHTML = related.map(r => {
    const isFav = favoriteIds.includes(Number(r.id));
    const disabled = Number(r.stock || 0) <= 0;
    return `<article class="card">
      <div class="card-media"><span class="flag">${r.badge || ''}</span><img src="${r.image}" alt="${r.name}"></div>
      <div class="card-body">
        <div class="small">${r.categoryLabel || ''}</div>
        <h3>${r.name}</h3>
        <div class="price-row">
          <div><div class="price">${Chacha.money(Number(r.price || 0))}</div><div class="old-price">${Number(r.oldPrice || 0) > 0 ? Chacha.money(Number(r.oldPrice || 0)) : ''}</div></div>
          <div class="muted">${Number(r.stock || 0) <= 0 ? 'Rupture' : 'Stock: ' + Number(r.stock || 0)}</div>
        </div>
        <div class="card-actions">
          <a class="btn btn-dark" href="produit.php?id=${r.id}">${disabled ? 'Voir' : 'Choisir tailles'}</a>
          <button class="btn btn-light" data-favorite-id="${r.id}" type="button" onclick="toggleFavoriteServer(${r.id})">${isFav ? 'Retirer des favoris' : 'Ajouter aux favoris'}</button>
          <a class="btn btn-light" href="produit.php?id=${r.id}">Voir</a>
        </div>
      </div>
    </article>`;
  }).join('');
});