<?php
declare(strict_types=1);


require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/product_admin_helpers.php';

$products = admin_load_products_db($pdo);
$success = get_flash('success');
$error = get_flash('error');
?>
<section class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
        <div>
            <span class="muted">CRUD produits MySQL</span>
            <h1>Produits</h1>
            <p class="muted">Ajout, modification et suppression directement en base de données.</p>
        </div>
        <a class="btn btn-dark" href="product-add.php">Ajouter un produit</a>
    </div>

    <?php if ($success): ?><div class="alert alert-success" style="margin-top:16px;"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error" style="margin-top:16px;"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <?php if (!$products): ?>
        <div class="alert alert-error" style="margin-top:16px;">Aucun produit trouvé en base.</div>
    <?php else: ?>
        <table class="admin-table" style="margin-top:20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Produit</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Ancien prix</th>
                    <th>Stock</th>
                    <th>Vedette</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= (int) $product['id'] ?></td>
                        <td><img src="../<?= htmlspecialchars((string) $product['image']) ?>" alt="<?= htmlspecialchars((string) $product['name']) ?>" style="width:56px;height:72px;object-fit:cover;border-radius:10px;"></td>
                        <td><?= htmlspecialchars((string) $product['name']) ?></td>
                        <td><?= htmlspecialchars((string) $product['categoryLabel']) ?></td>
                        <td><?= format_eur((float) $product['price']) ?></td>
                        <td><?= format_eur((float) ($product['oldPrice'] ?? 0)) ?></td>
                        <td><?= (int) ($product['stock'] ?? 0) ?></td>
                        <td><?= !empty($product['featured']) ? 'Oui' : 'Non' ?></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-light" href="product-edit.php?id=<?= (int) $product['id'] ?>">Modifier</a>
                                <form action="product-delete.php" method="post" onsubmit="return confirm('Supprimer ce produit de la BDD ?');">
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <button class="btn btn-light" type="submit">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>