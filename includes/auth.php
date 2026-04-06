<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/session.php';

function current_user(): ?array
{
    global $pdo;

    if (empty($_SESSION['user']['id'])) {
        return null;
    }

    $userId = (int) $_SESSION['user']['id'];

    $stmt = $pdo->prepare('
        SELECT id, first_name, last_name, email, phone, role, is_active
        FROM users
        WHERE id = :id
        LIMIT 1
    ');
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if (!$user || (int) $user['is_active'] !== 1) {
        unset($_SESSION['user']);
        return null;
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'phone' => $user['phone'] ?? null,
        'role' => $user['role'] ?? 'client',
        'is_active' => (int) $user['is_active'],
    ];

    return $_SESSION['user'];
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function login_user(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'phone' => $user['phone'] ?? null,
        'role' => $user['role'] ?? 'client',
        'is_active' => isset($user['is_active']) ? (int) $user['is_active'] : 1,
    ];
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'] ?? '',
            (bool) $params['secure'],
            (bool) $params['httponly']
        );
    }

    session_destroy();
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['flash']['error'] = 'Votre session a expiré ou votre compte n\'existe plus. Veuillez vous reconnecter.';
        header('Location: login.php');
        exit;
    }
}

function redirect_if_logged_in(): void
{
    if (is_logged_in()) {
        $user = current_user();

        if (($user['role'] ?? '') === 'admin') {
            header('Location: admin/dashboard.php');
            exit;
        }

        header('Location: account.php');
        exit;
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

function get_flash(string $type): ?string
{
    if (!isset($_SESSION['flash'][$type])) {
        return null;
    }

    $message = $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);

    return $message;
}
?>