<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

function require_admin(): void
{
    $user = current_user();

    if (!$user || ($user['role'] ?? '') !== 'admin') {
        set_flash('error', 'Accès administrateur requis.');
        header('Location: ../login.php');
        exit;
    }
}
?>