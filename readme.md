# 本リポジトリについて

下記記事を参考に実装したログインフォーム。
本readme.mdは実装に際し調べた内容のメモ。

[MySQLのバージョン確認方法 - Qiita](https://qiita.com/rokumura7/items/b270acb9550efddd5fe5)

# database.php

DB接続と特殊文字エンコードの処理を記述しているファイル。

## `htmlspecialchars()`

HTMLにおいて特殊な意味を持つ特殊文字を、HTMLエンティティに変換する。
通常、オプションには`ENT_QUOTES | ENT_HTML5`を指定すればよい。

|変換前|変換後|備考|
|:--|:--|:--|
|&|`&amp;`||
|"|`&quot;`||
|'|`&apos;`|ENT_QUOTES及びENT_HTML5が指定されている場合|
|<|`&lt;`||
|>|`&gt;`||

- [Entity (エンティティ) - MDN Web Docs 用語集: ウェブ関連用語の定義 | MDN](https://developer.mozilla.org/ja/docs/Glossary/Entity)

## PDOのオプション

TODO: 後で調べる

# index.php

## 処理の流れ

フォーム送信時の遷移先を自身にし、判定を行う。
入力内容に問題がなければmain.phpに遷移。
問題があった場合はエラーを表示する。

## `prepare`メソッドの`?`について

後から値が代入されることを示す。
ここでは、SQL実行時に`execute`メソッドの引数として値を配列として渡している。

```php
$stmt = $pdo->prepare('SELECT * FROM User WHERE user_name = ?');
// SQLのパラメータを設定
$params = [];
$params[] = $userName;
// SQL実行
$stmt->execute($params); // '?'には$userNameが代入される
```

または`bindValue`メソッドを使用して渡すこともできる。

```php
$stmt = $pdo->prepare('SELECT * FROM User WHERE user_name = :userName');
$stmt->bindValue(':userName', $userName);
$stmt->execute();
```

## 使用関数

### `session_start()`

セッション変数(`$_SESSION`)を利用するための関数。
新しいセッションの開始、あるいは既存のセッションを再開する。

### `passeord_verify()`

入力したパスワードが、`password_hash()`によって生成されたハッシュにマッチするか調べる。
元のデータを不規則な文字列に置換する処理をハッシュ化と呼び、その置換された文字列がハッシュ。

### `session_regenerate_id()`

セッションを継続したまま、セッションIDを更新する。
ログイン状態をセッション変数に書き込む直前に実行することで、ログイン情報の情報の盗聴やセッションハイジャックを防ぐ。

```php
session_regenerate_id(bool $delete_old_session = false): bool
```

- [php - session_regenerate_id()の必要性に関して - スタック・オーバーフロー](https://ja.stackoverflow.com/questions/24304/session-regenerate-id%E3%81%AE%E5%BF%85%E8%A6%81%E6%80%A7%E3%81%AB%E9%96%A2%E3%81%97%E3%81%A6) 

# SQLについて

MySQL 5.7.34では`UNIQUE KEY`の行でエラー。
以下のように直すとようになおすとテーブルが作成できた。

```diff
CREATE TABLE `User` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'AI',
  `user_name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '氏名',
  `password` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'パスワード',
  PRIMARY KEY (`id`),
-  UNIQUE KEY `user_name` (`user_name`)
+  UNIQUE KEY (`user_name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4;

INSERT INTO `User` (`id`, `user_name`, `password`)
VALUES
    (1,'user','$2y$10$ecRmAWY4n/jLa0tTzIaG7.SMhb1TfdROy3nXeG5aVZorUX1n6/WHO');
```