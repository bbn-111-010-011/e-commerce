<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$user = current_user();
$firstName = trim((string) ($_POST['first_name'] ?? ''));
$lastName = trim((string) ($_POST['last_name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));

if ($firstName === '' || $lastName === '' || $email === '') {
    set_flash('error', 'Veuillez remplir les champs obligatoires.');
    header('Location: ../account-profile.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('error', 'Adresse email invalide.');
    header('Location: ../account-profile.php');
    exit;
}

$check = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
$check->execute([
    'email' => $email,
    'id' => (int) $user['id'],
]);

if ($check->fetch()) {
    set_flash('error', 'Cette adresse email est déjà utilisée par un autre compte.');
    header('Location: ../account-profile.php');
    exit;
}

$stmt = $pdo->prepare('
    UPDATE users
    SET first_name = :first_name,
        last_name = :last_name,
        email = :email,
        phone = :phone,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = :id
');
$stmt->execute([
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'phone' => $phone !== '' ? $phone : null,
    'id' => (int) $user['id'],
]);

$_SESSION['user']['first_name'] = $firstName;
$_SESSION['user']['last_name'] = $lastName;
$_SESSION['user']['email'] = $email;
$_SESSION['user']['phone'] = $phone !== '' ? $phone : null;

set_flash('success', 'Vos coordonnées ont bien été mises à jour.');
header('Location: ../account-profile.php');
exit;
?>