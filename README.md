## FuelPHP Test Utility Package

実験的なパッケージです。
このパッケージは PHPUnit テスト時に、

* 実行環境のDB内に、接頭語に`test_`が付いたテスト用のテーブルを作成
 + テスト終了時に削除
* バリデーションのテストを簡潔に行うことができる 
* テスト時に実行されたSQLをコンソールに出力

### 使用方法

#### 1. `fuel/app/bootstrap.php` の `Fuel::init('config.php');` 部分より前に下記を追記します。

```
/* testutil パッケージで Fuel\Core\Profile のクラス拡張するため */
switch (Fuel::$env) {
    case Fuel::DEVELOPMENT:
    case Fuel::TEST:
        Package::load('testutil');
        break;
}
```

#### 2. `fuel/app/config/package.php` に下記を追記します。

```
	'sources' => array(
		'github.com/kawamurayuto',
	),
```