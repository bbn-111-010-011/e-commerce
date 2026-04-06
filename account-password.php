<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

require_login();
$user = current_user();
$pageTitle = 'Modifier mon mot de passe - Chacha';
$error = get_flash('error');
$success = get_flash('success');

require_once __DIR__ . '/includes/header.php';
?>

<section class="card" style="max-width:760px;">
    <span class="muted">Sécurité</span>
    <h1>Modifier mon mot de passe</h1>

    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form action="actions/account_password_update.php" method="post" novalidate>
        <div class="form-group">
            <label for="current_password">Mot de passe actuel</label>
            <input id="current_password" name="current_password" type="password" required>
        </div>

        <div class="form-group">
            <label for="new_password">Nouveau mot de passe</label>
            <input id="new_password" name="new_password" type="password" required>
        </div>

        <div class="form-group">
            <label for="new_password_confirm">Confirmer le nouveau mot de passe</label>
            <input id="new_password_confirm" name="new_password_confirm" type="password" required>
        </div>

        <div class="actions">
            <button class="btn btn-dark" type="submit">Mettre à jour</button>
            <a class="btn btn-light" href="account.php">Annuler</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>