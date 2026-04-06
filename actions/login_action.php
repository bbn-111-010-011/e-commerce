<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

verify_csrf_or_die();

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if ($email === '' || $password === '') {
    set_flash('error', 'Veuillez remplir tous les champs.');
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->prepare('
    SELECT id, first_name, last_name, email, phone, password_hash, role, is_active
    FROM users
    WHERE email = :email
    LIMIT 1
');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    set_flash('error', 'Email ou mot de passe incorrect.');
    header('Location: ../login.php');
    exit;
}

if ((int) $user['is_active'] !== 1) {
    set_flash('error', 'Votre compte est désactivé.');
    header('Location: ../login.php');
    exit;
}

login_user($user);
set_flash('success', 'Connexion réussie.');

if (($user['role'] ?? '') === 'admin') {
    header('Location: ../admin/dashboard.php');
    exit;
}

header('Location: ../account.php');
exit;
?>