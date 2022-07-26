<?php
/**
 * index.php
 */
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

session_start();

require_once './database.php';

// エラーを格納する変数
$err = [];

if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
    // INPUT_SERVER ?
    $userName = filter_input(INPUT_POST, 'user_name');
    $password = filter_input(INPUT_POST, 'password');

    if ($userName === '') {
        $err['user_name'] = 'ユーザー名は入力必須です。';
    }
    if ($password === '') {
        $err['password'] = 'パスワードは入力必須です。';
    }

    if (count($err) === 0) {
        $pdo = connect();
        $stmt = $pdo->prepare('SELECT * FROM User WHERE user_name = ?');

        // SQLのパラメータを設定
        $params = [];
        $params[] = $userName;

        // SQL実行
        $stmt->execute($params);

        // レコードセットを取得（fetchAllを使用するとメモリを大量に消費するとのことなのでfetchで取得）
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $password_hash = $row['password'];

        // パスワード一致
        if (password_verify($password, $password_hash)) {
            // セッションIDを更新（引数のtrueは、古いセッションIDを削除する指定）
            session_regenerate_id(true);
            $_SESSION['login_user'] = $row;
            // main.phpに遷移
            header('Location:main.php');
            return;
        }

        $err['login'] = 'ログインに失敗しました。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <form action="" method="post">
            <?php if (isset($err['login'])): ?>
                <p class="error"><?= h($err['login']) ?></p>
            <?php endif; ?>
            <p>
                <label>
                    ユーザー名
                    <input type="text" id="user_id" name="user_name">
                    <?php if (isset($err['user_name'])): ?>
                        <p class="error"><?= h($err['user_name']) ?></p>
                    <?php endif; ?>
                </label>
            </p>
            <p>
                <label>
                    パスワード
                    <input type="password" id="password" name="password">
                    <?php if (isset($err['password'])): ?>
                        <p class="error"><?= h($err['password']) ?></p>
                    <?php endif; ?>
                </label>
            </p>
            <p><button type="submit">ログイン</button></p>
            <p><a href="adduser.php">新規ユーザー登録</a></p>
        </form>
    </div>
</body>
</html>
