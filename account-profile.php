<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

require_login();
$user = current_user();

if (($user['role'] ?? '') === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

$pageTitle = 'Mes coordonnées - Chacha';
$error = get_flash('error');
$success = get_flash('success');

require_once __DIR__ . '/includes/header.php';
?>

<section class="card" style="max-width:820px;">
    <span class="muted">Espace client</span>
    <h1>Mes coordonnées</h1>

    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form action="actions/account_profile_update.php" method="post" novalidate>
        <div class="grid grid-2">
            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input id="first_name" name="first_name" type="text" value="<?= htmlspecialchars((string) $user['first_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Nom</label>
                <input id="last_name" name="last_name" type="text" value="<?= htmlspecialchars((string) $user['last_name']) ?>" required>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= htmlspecialchars((string) $user['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input id="phone" name="phone" type="text" value="<?= htmlspecialchars((string) ($user['phone'] ?? '')) ?>">
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-dark" type="submit">Enregistrer</button>
            <a class="btn btn-light" href="account.php">Retour compte</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>