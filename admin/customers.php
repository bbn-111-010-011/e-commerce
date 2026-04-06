<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/admin_header.php';

$stmt = $pdo->query("
    SELECT id, first_name, last_name, email, phone, is_active, created_at
    FROM users
    WHERE role = 'client'
    ORDER BY id DESC
");
$customers = $stmt->fetchAll();
?>

<section class="card">
    <span class="muted">Clients</span>
    <h1>Gestion des clients</h1>

    <?php if (!$customers): ?>
        <p>Aucun client inscrit.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Actif</th>
                    <th>Inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= (int) $customer['id'] ?></td>
                        <td><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td><?= htmlspecialchars((string) $customer['phone']) ?></td>
                        <td><?= (int) $customer['is_active'] === 1 ? 'Oui' : 'Non' ?></td>
                        <td><?= htmlspecialchars($customer['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>