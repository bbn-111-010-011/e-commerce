<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/csrf.php';

redirect_if_logged_in();

$pageTitle = 'Créer un compte - Chacha';
$error = get_flash('error');
$success = get_flash('success');

require_once __DIR__ . '/includes/header.php';
?>

<section class="card" style="max-width: 820px; margin: 0 auto;">
    <span class="muted">Espace client</span>
    <h1>Créer un compte</h1>
    <p class="muted">Créez votre compte client Chacha pour retrouver vos informations et vos prochaines commandes.</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="actions/register_action.php" method="post" novalidate>
        <input type="hidden" name="redirect_to" value="<?= htmlspecialchars((string) ($_GET['redirect'] ?? '')) ?>">
        <?= csrf_input() ?>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="first_name">Prénom</label>
                <input id="first_name" name="first_name" type="text" required>
            </div>

            <div class="form-group">
                <label for="last_name">Nom</label>
                <input id="last_name" name="last_name" type="text" required>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input id="email" name="email" type="email" required>
            </div>

            <div class="form-group">
                <label for="phone">Téléphone</label>
                <input id="phone" name="phone" type="text" placeholder="Optionnel">
            </div>
        </div>

        <div class="grid grid-2">
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input id="password_confirm" name="password_confirm" type="password" required>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-dark" type="submit">Créer mon compte</button>
            <a class="btn btn-light" href="login.php">J'ai déjà un compte</a>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>