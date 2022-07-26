<?php
/**
 * database.php
 */
declare(strict_types=1);

/**
 * 特殊文字エンコード
 */
function h(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'utf-8');
}

/**
 * DB接続
 */
function connect(): PDO
{
    $dsn = 'mysql:host=localhost;dbname=login_sample;charset=utf8mb4';
    $userName = 'root';
    $password = 'root';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $userName, $password, $options);
    return $pdo;
}
