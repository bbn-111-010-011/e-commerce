
document.addEventListener('DOMContentLoaded', async()=>{
  const products=await Chacha.loadProducts();
  const cart=Chacha.getCart();
  const tbody=document.getElementById('cart-body');
  let total=0;
  tbody.innerHTML=cart.map(item=>{
    const p=products.find(x=>x.id===item.productId);
    if(!p) return '';
    const line=p.price*item.qty;
    total+=line;
    return `<tr><td>${p.name}</td><td>${item.size}</td><td>${item.qty}</td><td>${Chacha.money(p.price)}</td><td>${Chacha.money(line)}</td></tr>`;
  }).join('') || `<tr><td colspan="5">Votre panier est vide.</td></tr>`;
  document.getElementById('cart-total').textContent=Chacha.money(total);
});
