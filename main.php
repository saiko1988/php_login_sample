<?php
/**
 * main..php
 */
declare(strict_types=1);
ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

require_once './database.php';
$login_user = $_SESSION['login_user'];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>main</title>
</head>
<body>
    <?php foreach ($login_user as $key => $value): ?>
        <p><?= h($key) ?>：<?= h($value) ?></p>
    <?php endforeach; ?>
</body>
</html>
