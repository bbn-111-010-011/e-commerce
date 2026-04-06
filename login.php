<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

redirect_if_logged_in();

$pageTitle = 'Connexion - Chacha';
$error = get_flash('error');
$success = get_flash('success');

require_once __DIR__ . '/includes/header.php';
?>

<section class="card" style="max-width: 680px; margin: 0 auto;">
    <span class="muted">Espace client</span>
    <h1>Connexion</h1>
    <p class="muted">Connectez-vous pour accéder à votre compte client.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="actions/login_action.php" method="post" novalidate>
        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars((string) ($_GET['redirect'] ?? '')) ?>">
        <?= csrf_input() ?>

        <div class="form-group">
            <label for="email">Adresse email</label>
            <input id="email" name="email" type="email" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required>
        </div>

        <div class="actions">
            <button class="btn btn-dark" type="submit">Se connecter</button>
            <a class="btn btn-light" href="register.php">Créer un compte</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>