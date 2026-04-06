document.addEventListener('DOMContentLoaded', async()=>{
  const wrap=document.getElementById('catalog-grid');
  const filtersWrap=document.getElementById('catalog-filters');
  const search=document.getElementById('searchInput');
  const products=await Chacha.loadProducts();
  let active='all';

  const categoryOrder=['robe-soiree','caftan-femme','caftan-enfant','karakou-femme','karakou-enfant'];
  const categoryLabels={
    'robe-soiree':'Robe de soirée',
    'caftan-femme':'Caftan femme',
    'caftan-enfant':'Caftan enfant',
    'karakou-femme':'Karakou femme',
    'karakou-enfant':'Karakou enfant'
  };

  const foundCategories=[...new Set(products.map(p=>p.category).filter(Boolean))];
  foundCategories.sort((a,b)=>{
    const ia=categoryOrder.indexOf(a); const ib=categoryOrder.indexOf(b);
    if (ia === -1 && ib === -1) return a.localeCompare(b);
    if (ia === -1) return 1;
    if (ib === -1) return -1;
    return ia-ib;
  });

  if (filtersWrap) {
    filtersWrap.innerHTML = `<button class="pill active" data-category="all">Tous</button>` +
      foundCategories.map(cat=>`<button class="pill" data-category="${cat}">${categoryLabels[cat] || cat}</button>`).join('');
  }

  function bindPills(){
    const pills=document.querySelectorAll('.pill[data-category]');
    pills.forEach(btn=>btn.addEventListener('click',()=>{
      pills.forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      active=btn.dataset.category;
      render();
    }));
  }

  function render(){
    const favoriteIds = window.CHACHA_FAVORITES || [];
    const q=(search?.value||'').toLowerCase().trim();
    const filtered=products.filter(p=>{
      const catOk=active==='all'||p.category===active;
      const text=`${p.name||''} ${(p.categoryLabel||'')}`.toLowerCase();
      const textOk=!q||text.includes(q);
      return catOk&&textOk;
    });

    wrap.innerHTML=filtered.map(p=>{
      const isFav = favoriteIds.includes(Number(p.id));
      return `<article class="card">
      <div class="card-media"><span class="flag">${p.badge || ''}</span><img src="${p.image}" alt="${p.name}"></div>
      <div class="card-body">
        <div class="small">${p.categoryLabel || ''}</div><h3>${p.name}</h3>
        <div class="price-row"><div><div class="price">${Chacha.money(p.price)}</div><div class="old-price">${Chacha.money(p.oldPrice || 0)}</div></div><div class="muted">Stock: ${p.stock || 0}</div></div>
        <div class="card-actions">
          <button class="btn btn-dark" type="button" onclick="addToCartServer(${p.id},1,'M')">Ajouter au panier</button>
          <button class="btn btn-light" data-favorite-id="${p.id}" type="button" onclick="toggleFavoriteServer(${p.id})">${isFav ? 'Retirer des favoris' : 'Ajouter aux favoris'}</button>
          <a class="btn btn-light" href="produit.php?id=${p.id}">Voir</a>
        </div>
      </div></article>`;
    }).join('');
  }

  bindPills();
  search?.addEventListener('input', render);
  render();
});