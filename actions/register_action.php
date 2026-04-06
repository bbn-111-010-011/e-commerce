<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

verify_csrf_or_die();

$redirectTo = trim((string) ($_POST['redirect_to'] ?? ''));
$allowedRedirects = ['shop.php', 'catalogue.php', 'produit.php', 'panier.php', 'favorites.php', 'checkout.php', 'account.php'];
if ($redirectTo !== '') {
    $baseRedirect = strtok($redirectTo, '?') ?: $redirectTo;
    if (!in_array($baseRedirect, $allowedRedirects, true)) {
        $redirectTo = '';
    }
}

$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName = trim((string) ($_POST['last_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $passwordConfirm === '') {
    set_flash('error', 'Veuillez remplir tous les champs obligatoires.');
    header('Location: ../register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Adresse email invalide.');
    header('Location: ../register.php');
    exit;
}

if (mb_strlen($password) < 8) {
    set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
    header('Location: ../register.php');
    exit;
}

if ($password !== $passwordConfirm) {
    set_flash('error', 'Les mots de passe ne correspondent pas.');
    header('Location: ../register.php');
    exit;
}

$checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$checkStmt->execute(['email' => $email]);

if ($checkStmt->fetch()) {
    set_flash('error', 'Cette adresse email est déjà utilisée.');
    header('Location: ../register.php');
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$insertStmt = $pdo->prepare('
    INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active)
    VALUES (:first_name, :last_name, :email, :phone, :password_hash, :role, :is_active)
');

$insertStmt->execute([
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'phone' => $phone !== '' ? $phone : null,
    'password_hash' => $passwordHash,
    'role' => 'client',
    'is_active' => 1,
]);

$userId = (int) $pdo->lastInsertId();

$userStmt = $pdo->prepare('SELECT id, first_name, last_name, email, phone, role, is_active FROM users WHERE id = :id LIMIT 1');
$userStmt->execute(['id' => $userId]);
$user = $userStmt->fetch();

if (!$user) {
    set_flash('error', 'Compte créé, mais connexion automatique impossible.');
    header('Location: ../login.php');
    exit;
}

login_user($user);
set_flash('success', 'Votre compte a bien été créé.');

if (($user['role'] ?? '') === 'admin') {
    header('Location: ../admin/dashboard.php');
    exit;
}

header('Location: ../' . ($redirectTo !== '' ? $redirectTo : 'account.php'));
exit;
?>