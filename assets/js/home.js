document.addEventListener('DOMContentLoaded', async()=>{
  const products=await Chacha.loadProducts();
  const favoriteIds = window.CHACHA_FAVORITES || [];

  const sections={
    'home-featured':products.filter(p=>p.featured).slice(0,8),
    'home-caftan-femme':products.filter(p=>p.category==='caftan-femme').slice(0,4),
    'home-robe':products.filter(p=>p.category==='robe-soiree').slice(0,4),
    'home-karakou':products.filter(p=>String(p.category || '').includes('karakou')).slice(0,4),
  };

  function card(p){
    const isFav = favoriteIds.includes(Number(p.id));
    return `<article class="card">
      <div class="card-media"><span class="flag">${p.badge || ''}</span><img src="${p.image}" alt="${p.name}"></div>
      <div class="card-body">
        <div class="small">${p.categoryLabel || ''}</div><h3>${p.name}</h3>
        <div class="price-row"><div><div class="price">${Chacha.money(p.price)}</div><div class="old-price">${Chacha.money(p.oldPrice || 0)}</div></div><div class="muted">${p.color || ''}</div></div>
        <div class="card-actions">
          <button class="btn btn-dark" type="button" onclick="addToCartServer(${p.id},1,'M')">Ajouter au panier</button>
          <button class="btn btn-light" data-favorite-id="${p.id}" type="button" onclick="toggleFavoriteServer(${p.id})">${isFav ? 'Retirer des favoris' : 'Ajouter aux favoris'}</button>
          <a class="btn btn-light" href="produit.php?id=${p.id}">Voir</a>
        </div>
      </div></article>`;
  }

  Object.entries(sections).forEach(([id,list])=>{ const el=document.getElementById(id); if(el) el.innerHTML=list.map(card).join(''); });

  const spotlight=document.getElementById('hero-spotlight');
  if (spotlight) {
    spotlight.innerHTML=products.slice(0,4).map(p=>`<div class="hero-item"><img class="hero-thumb" src="${p.image}" alt="${p.name}"><div><strong>${p.name}</strong><div class="muted">${p.categoryLabel || ''}</div></div><div><strong>${Chacha.money(p.price)}</strong></div></div>`).join('');
  }
});