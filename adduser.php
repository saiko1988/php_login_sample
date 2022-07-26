<?php
/**
 * adduser.php
 */
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

require_once './database.php';

$err = [];

// 「ログイン」ボタンが押されて、POST送信のとき
if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    $userName = filter_input(INPUT_POST, 'user_name');
    $password = filter_input(INPUT_POST, 'password');
    $password_conf = filter_input(INPUT_POST, 'password_conf');

    if ($userName === '') {
        $err['userName'] = 'ユーザー名は入力必須です。';
    }
    if ($password === '') {
        $err['password'] = 'パスワードは入力必須です。';
    }
    if ($password !== $password_conf) {
        $err['password'] = 'パスワードが一致しません。';
    }

    if (count($err) === 0) {
        $pdo = connect();
        $stmt = $pdo->prepare(
            'INSERT INTO `User` (`id`, `user_name`, `password`) VALUES (null, ?, ?)',
        );

        $params = [];
        $params[] = $userName;
        $params[] = password_hash($password, PASSWORD_DEFAULT);

        // SQL実行
        $success = $stmt->execute($params);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <?php if (count($err) > 0): ?>
        <?php foreach ($err as $e): ?>
            <p class="error"><?= h($e) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (isset($success) && $success): ?>
        <p>登録に成功しました。</p>
        <p><a href="index.php">こちら</a>からログインしてください。</p>
    <?php else: ?>
        <form action="" method="post">
            <p>
                <label>
                    ユーザー名
                    <input type="text" id="user_id" name="user_name">
                </label>
            </p>
            <p>
                <label>
                    パスワード
                    <input type="password" id="password" name="password">
                </label>
            </p>
            <p>
                <label>
                    確認用パスワード
                    <input type="password" id="password_conf" name="password_conf">
                </label>
            </p>
            <p><button type="submit">ログイン</button></p>
        </form>
    <?php endif; ?>
</body>
</html>