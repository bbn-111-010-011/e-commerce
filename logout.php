<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

logout_user();
session_start();
$_SESSION['flash']['success'] = 'Vous avez été déconnecté avec succès.';

header('Location: login.php');
exit;
?>