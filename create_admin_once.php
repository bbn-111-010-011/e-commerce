<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

$email = 'admin.chachaboutique@gmail.com';
$password = 'AdminChacha123!';
$hash = password_hash($password, PASSWORD_DEFAULT);

$check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$check->execute(['email' => $email]);
$existingId = $check->fetchColumn();

if ($existingId) {
    $stmt = $pdo->prepare('
        UPDATE users
        SET first_name = "admin",
            last_name = "bouabboune",
            password_hash = :password_hash,
            role = "admin",
            is_active = 1
        WHERE id = :id
    ');
    $stmt->execute([
        'password_hash' => $hash,
        'id' => (int) $existingId,
    ]);

    echo "Admin mis à jour avec succès.<br>";
} else {
    $stmt = $pdo->prepare('
        INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active)
        VALUES ("admin", "bouabboune", :email, NULL, :password_hash, "admin", 1)
    ');
    $stmt->execute([
        'email' => $email,
        'password_hash' => $hash,
    ]);

    echo "Admin créé avec succès.<br>";
}

echo "Email : " . htmlspecialchars($email) . "<br>";
echo "Mot de passe : " . htmlspecialchars($password) . "<br>";
echo "<strong>Supprimez ce fichier après utilisation.</strong>";
?>