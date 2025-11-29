# セットアップガイド（初心者向け・詳細版）

このガイドでは、勤怠管理アプリケーションを初めて起動する手順を、画面キャプチャを想定しながら詳しく説明します。

## 📋 目次

1. [必要なソフトウェアのインストール](#必要なソフトウェアのインストール)
2. [プロジェクトの準備](#プロジェクトの準備)
3. [Dockerコンテナの起動](#dockerコンテナの起動)
4. [データベースのセットアップ](#データベースのセットアップ)
5. [アプリケーションの確認](#アプリケーションの確認)
6. [トラブルシューティング](#トラブルシューティング)

---

## 必要なソフトウェアのインストール

### 1. Docker Desktopのインストール

#### Macの場合

1. [Docker Desktop for Mac](https://www.docker.com/products/docker-desktop/)にアクセス
2. 「Download for Mac」をクリック
3. ダウンロードした`.dmg`ファイルを開く
4. DockerアイコンをApplicationsフォルダにドラッグ&ドロップ
5. ApplicationsフォルダからDockerを起動
6. 初回起動時は管理者権限の入力が求められます
7. メニューバーにDockerアイコンが表示されれば完了

#### Windowsの場合

1. [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/)にアクセス
2. 「Download for Windows」をクリック
3. ダウンロードしたインストーラーを実行
4. インストール完了後、再起動を求められる場合があります
5. 再起動後、Docker Desktopを起動
6. タスクバーにDockerアイコンが表示されれば完了

**確認方法**: ターミナル（Mac）またはコマンドプロンプト（Windows）で以下を実行：

```bash
docker --version
```

バージョン番号が表示されれば成功です。

### 2. Composerのインストール

#### Macの場合

ターミナルで以下を実行：

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

#### Windowsの場合

1. [Composer公式サイト](https://getcomposer.org/download/)にアクセス
2. 「Composer-Setup.exe」をダウンロード
3. インストーラーを実行し、指示に従ってインストール

**確認方法**:

```bash
composer --version
```

バージョン番号が表示されれば成功です。

---

## プロジェクトの準備

### ステップ1: ターミナルを開く

- **Mac**: アプリケーション > ユーティリティ > ターミナル
- **Windows**: スタートメニュー > コマンドプロンプト または PowerShell

### ステップ2: プロジェクトディレクトリに移動

```bash
cd /Users/a0913/test4
```

**Windowsの場合**（パスが異なる場合）:
```bash
cd C:\Users\a0913\test4
```

### ステップ3: 現在のディレクトリを確認

```bash
pwd
```

**Macの場合**: `/Users/a0913/test4` と表示されればOK
**Windowsの場合**: `cd` コマンドで現在のディレクトリを確認

### ステップ4: ファイルの確認

```bash
ls
```

**Windowsの場合**:
```bash
dir
```

以下のファイルが表示されればOK：
- `docker-compose.yml`
- `Dockerfile`
- `composer.json`
- `.env.example`

---

## Dockerコンテナの起動

### ステップ1: Docker Desktopが起動しているか確認

メニューバー（Mac）またはタスクバー（Windows）にDockerアイコンがあることを確認します。
アイコンが緑色または実行中であればOKです。

### ステップ2: 依存関係のインストール

```bash
composer install
```

**実行時間**: 初回は5〜10分かかる場合があります

**正常な出力例**:
```
Loading composer repositories with package information
Installing dependencies (including require-dev) from lock file
...
```

**エラーが出た場合**:
- インターネット接続を確認
- `composer --version`でComposerがインストールされているか確認

### ステップ3: 環境変数ファイルの作成

```bash
cp .env.example .env
```

**Windowsの場合**:
```bash
copy .env.example .env
```

**確認方法**:
```bash
ls -la .env
```

ファイルが存在すればOKです。

### ステップ4: Dockerコンテナの起動

```bash
docker-compose up -d
```

**実行時間**: 初回は5〜10分かかる場合があります（イメージのダウンロード）

**正常な出力例**:
```
Creating network "test4_attendance-network" ... done
Creating attendance_db ... done
Creating attendance_mailtrap ... done
Creating attendance_app ... done
```

### ステップ5: コンテナの状態確認

```bash
docker-compose ps
```

**正常な出力例**:
```
NAME                  STATUS
attendance_app        Up
attendance_db         Up
attendance_mailtrap   Up
```

すべて「Up」になっていれば成功です。

---

## データベースのセットアップ

### ステップ1: アプリケーションキーの生成

```bash
docker-compose exec app php artisan key:generate
```

**正常な出力例**:
```
Application key set successfully.
```

### ステップ2: データベースマイグレーション

```bash
docker-compose exec app php artisan migrate
```

**正常な出力例**:
```
Migration table created successfully.
Migrating: 2024_01_01_000001_create_users_table
Migrated:  2024_01_01_000001_create_users_table
...
```

### ステップ3: 管理者ユーザーの作成（オプション）

テスト用の管理者ユーザーを作成します：

```bash
docker-compose exec app php artisan tinker
```

tinkerが起動したら、以下のコマンドを1行ずつ入力してEnterキーを押します：

```php
$user = new App\Models\User();
$user->name = '管理者';
$user->email = 'admin@example.com';
$user->password = bcrypt('password123');
$user->role = 'admin';
$user->email_verified_at = now();
$user->save();
exit
```

**注意**: `exit`と入力してEnterキーを押すと、tinkerが終了します。

---

## アプリケーションの確認

### ステップ1: ブラウザでアクセス

以下のURLをブラウザで開きます：

- **一般ユーザーログイン**: http://localhost:8000/login
- **管理者ログイン**: http://localhost:8000/admin/login

### ステップ2: 会員登録のテスト

1. http://localhost:8000/register にアクセス
2. フォームに情報を入力：
   - 名前: テストユーザー
   - メールアドレス: test@example.com
   - パスワード: password123
   - パスワード確認: password123
3. 「登録する」ボタンをクリック
4. 打刻画面が表示されれば成功

### ステップ3: Mailtrapでメール確認

1. http://localhost:8025 にアクセス
2. 会員登録時に送信された認証メールが表示されます
3. メール内のリンクをクリックしてメール認証を完了

---

## トラブルシューティング

### 問題1: `docker-compose up -d`がエラーになる

**エラーメッセージ例**:
```
ERROR: Couldn't connect to Docker daemon
```

**解決方法**:
1. Docker Desktopが起動しているか確認
2. Docker Desktopを再起動
3. ターミナルを再起動して再度実行

### 問題2: ポートが既に使用されている

**エラーメッセージ例**:
```
Error: bind: address already in use
```

**解決方法**:
1. 使用中のポートを確認：
   ```bash
   lsof -i :8000
   ```
2. 別のポートを使用する場合は、`docker-compose.yml`の`8000:8000`を`8001:8000`などに変更

### 問題3: データベース接続エラー

**エラーメッセージ例**:
```
SQLSTATE[HY000] [2002] Connection refused
```

**解決方法**:
1. MySQLコンテナが起動しているか確認：
   ```bash
   docker-compose ps db
   ```
2. コンテナを再起動：
   ```bash
   docker-compose restart db
   ```
3. 少し待ってから再度マイグレーションを実行

### 問題4: ページが表示されない（404エラー）

**解決方法**:
1. アプリケーションコンテナが起動しているか確認：
   ```bash
   docker-compose ps app
   ```
2. ログを確認：
   ```bash
   docker-compose logs app
   ```
3. コンテナを再起動：
   ```bash
   docker-compose restart app
   ```

### 問題5: Composer installがエラーになる

**解決方法**:
1. インターネット接続を確認
2. Composerのバージョンを確認：
   ```bash
   composer --version
   ```
3. Composerを最新版に更新：
   ```bash
   composer self-update
   ```

---

## よく使うコマンド一覧

### コンテナの操作

```bash
# コンテナの起動
docker-compose up -d

# コンテナの停止
docker-compose down

# コンテナの再起動
docker-compose restart

# コンテナの状態確認
docker-compose ps

# ログの確認（リアルタイム）
docker-compose logs -f app

# すべてのログを確認
docker-compose logs
```

### Laravelのコマンド

```bash
# マイグレーション実行
docker-compose exec app php artisan migrate

# マイグレーションロールバック（1つ前の状態に戻す）
docker-compose exec app php artisan migrate:rollback

# すべてのマイグレーションをリセット
docker-compose exec app php artisan migrate:fresh

# キャッシュクリア
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# ルート一覧の確認
docker-compose exec app php artisan route:list
```

### データベースの操作

```bash
# MySQLに接続
docker-compose exec db mysql -u attendance_user -ppassword attendance_db

# データベースのバックアップ
docker-compose exec db mysqldump -u attendance_user -ppassword attendance_db > backup.sql
```

---

## 次のステップ

セットアップが完了したら、以下を試してみてください：

1. **会員登録**: 新しいユーザーを作成
2. **打刻機能**: 出勤・休憩・退勤をテスト
3. **勤怠一覧**: 月次勤怠を確認
4. **修正申請**: 勤怠の修正申請をテスト
5. **管理者機能**: 管理者でログインして各種機能を確認

---

## サポート

問題が解決しない場合は、以下の情報を含めて質問してください：

- 実行したコマンド
- エラーメッセージの全文
- 使用しているOS（Mac/Windows）
- Docker Desktopのバージョン
- Composerのバージョン

