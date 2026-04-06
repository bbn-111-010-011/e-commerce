<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$user = current_user();
$currentPassword = (string) ($_POST['current_password'] ?? '');
$newPassword = (string) ($_POST['new_password'] ?? '');
$newPasswordConfirm = (string) ($_POST['new_password_confirm'] ?? '');

if ($currentPassword === '' || $newPassword === '' || $newPasswordConfirm === '') {
    set_flash('error', 'Veuillez remplir tous les champs.');
    header('Location: ../account-password.php');
    exit;
}

if (mb_strlen($newPassword) < 8) {
    set_flash('error', 'Le nouveau mot de passe doit contenir au moins 8 caractères.');
    header('Location: ../account-password.php');
    exit;
}

if ($newPassword !== $newPasswordConfirm) {
    set_flash('error', 'Les nouveaux mots de passe ne correspondent pas.');
    header('Location: ../account-password.php');
    exit;
}

$stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :id LIMIT 1");
$stmt->execute(['id' => (int) $user['id']]);
$passwordHash = (string) ($stmt->fetchColumn() ?: '');

if ($passwordHash === '' || !password_verify($currentPassword, $passwordHash)) {
    set_flash('error', 'Mot de passe actuel incorrect.');
    header('Location: ../account-password.php');
    exit;
}

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$update = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :id");
$update->execute([
    'password_hash' => $newHash,
    'id' => (int) $user['id'],
]);

set_flash('success', 'Mot de passe mis à jour avec succès.');
header('Location: ../account-password.php');
exit;
?>