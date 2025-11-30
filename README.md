# 勤怠管理アプリケーション

Laravel、Fortify、Mailtrap、FormRequest、Dockerを使用した勤怠管理システムです。

## セットアップ手順（初心者向け）

### 前提条件

以下のソフトウェアがインストールされている必要があります：

1. **Docker Desktop** - [公式サイト](https://www.docker.com/products/docker-desktop/)からダウンロードしてインストール
2. **Composer** - [公式サイト](https://getcomposer.org/download/)からダウンロードしてインストール

### ステップ1: Docker Desktopの起動

1. Docker Desktopアプリケーションを起動します
2. 画面右下にDockerアイコンが表示され、緑色になっていることを確認します
3. これでDockerが使用可能な状態です

### ステップ2: プロジェクトディレクトリに移動

ターミナル（Mac）またはコマンドプロンプト（Windows）を開き、以下のコマンドを実行します：

```bash
cd /Users/a0913/test4
```

### ステップ3: 依存関係のインストール

以下のコマンドを実行して、Laravelに必要なパッケージをインストールします：

```bash
composer install
```

**注意**: 初回実行時は数分かかる場合があります。エラーが出た場合は、インターネット接続を確認してください。

### ステップ4: 環境変数ファイルの作成

`.env.example`ファイルを`.env`にコピーします：

```bash
cp .env.example .env
```

**Mac/Linuxの場合**:
```bash
cp .env.example .env
```

**Windowsの場合**:
```bash
copy .env.example .env
```

### ステップ5: アプリケーションキーの生成

Laravelのセキュリティキーを生成します：

```bash
php artisan key:generate
```

**注意**: このコマンドがエラーになる場合は、Dockerコンテナ内で実行する必要があります（ステップ6の後で実行）。

### ステップ6: Dockerコンテナの起動

以下のコマンドで、Laravel、MySQL、Mailtrapのコンテナを起動します：

```bash
docker-compose up -d
```

**説明**:
- `docker-compose up` = コンテナを起動
- `-d` = バックグラウンドで実行（デタッチモード）

**確認方法**: 以下のコマンドでコンテナが起動しているか確認できます：

```bash
docker-compose ps
```

3つのコンテナ（app、db、mailtrap）が「Up」状態になっていれば成功です。

### ステップ7: Dockerコンテナ内でコマンドを実行

Dockerコンテナ内でLaravelのコマンドを実行するには、以下の形式を使用します：

```bash
docker-compose exec app php artisan [コマンド]
```

### ステップ8: アプリケーションキーの生成（Docker内）

Dockerコンテナ内でアプリケーションキーを生成します：

```bash
docker-compose exec app php artisan key:generate
```

### ステップ9: データベースマイグレーション

データベースのテーブルを作成します：

```bash
docker-compose exec app php artisan migrate
```

**説明**: このコマンドで、users、attendances、breaksなどのテーブルが作成されます。

### ステップ10: ダミーデータの作成

管理者ユーザー、一般ユーザー、および勤怠記録のダミーデータを作成します：

```bash
docker-compose exec app php artisan db:seed
```

**説明**: このコマンドで、以下のデータが作成されます：
- **管理者ユーザー**: 1人（メール認証済み）
- **一般ユーザー**: 5人（メール認証済み）
- **勤怠記録**: 過去30日間の勤怠データ（出勤・退勤・休憩時間を含む）

**注意**: 既にデータが存在する場合は、重複して作成される可能性があります。データをクリアしてから実行する場合は、以下のコマンドを実行してください：

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

**警告**: `migrate:fresh`は既存のデータベースを削除して再作成するため、既存のデータは全て削除されます。

### ステップ11: ログイン情報

ダミーデータ作成後、以下のログイン情報でログインできます：

#### 管理者ユーザー
- **メールアドレス**: `admin@example.com`
- **パスワード**: `password123`
- **ログインURL**: http://localhost:8000/admin/login

#### 一般ユーザー
- **メールアドレス**: シーダーで自動生成されたメールアドレス（例: `user1@example.com`, `user2@example.com` など）
- **パスワード**: `password`
- **ログインURL**: http://localhost:8000/login

**注意**: 一般ユーザーのメールアドレスを確認するには、以下のコマンドを実行してください：

```bash
docker-compose exec app php artisan tinker
```

tinkerが起動したら、以下のコマンドを実行：

```php
App\Models\User::where('role', 'user')->get(['name', 'email']);
exit
```

### ステップ12: アプリケーションへのアクセス

ブラウザで以下のURLにアクセスします：

- **一般ユーザーログイン**: http://localhost:8000/login
- **管理者ログイン**: http://localhost:8000/admin/login

**注意**: ポート8000が使用できない場合は、`docker-compose.yml`でポート番号を変更するか、別のポートを使用してください。

### ステップ13: Mailtrap（メール確認）へのアクセス

開発環境で送信されたメールを確認するには：

- **Mailtrap UI**: http://localhost:8025

ここで、会員登録時に送信される認証メールを確認できます。

## よくある問題と解決方法

### 問題1: `composer install`がエラーになる

**解決方法**: 
- インターネット接続を確認
- Composerが正しくインストールされているか確認: `composer --version`

### 問題2: Dockerコンテナが起動しない

**解決方法**:
- Docker Desktopが起動しているか確認
- ポートが既に使用されていないか確認（3306、8000、8025など）
- エラーメッセージを確認: `docker-compose logs`

### 問題3: データベース接続エラー

**解決方法**:
- `.env`ファイルのデータベース設定を確認
- MySQLコンテナが起動しているか確認: `docker-compose ps`
- コンテナを再起動: `docker-compose restart`

### 問題4: ページが表示されない

**解決方法**:
- アプリケーションコンテナが起動しているか確認
- ログを確認: `docker-compose logs app`
- コンテナを再起動: `docker-compose restart app`

## 便利なコマンド一覧

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

# ログの確認
docker-compose logs app
docker-compose logs db
```

### Laravelのコマンド（Docker内で実行）

```bash
# マイグレーション実行
docker-compose exec app php artisan migrate

# マイグレーションロールバック
docker-compose exec app php artisan migrate:rollback

# キャッシュクリア
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

## 開発の流れ

1. Dockerコンテナを起動: `docker-compose up -d`
2. コードを編集
3. ブラウザで確認: http://localhost:8000
4. エラーが出た場合はログを確認: `docker-compose logs app`
5. 作業終了時はコンテナを停止: `docker-compose down`（オプション）

## 注意事項

- `.env`ファイルは機密情報を含むため、Gitにコミットしないでください（既に.gitignoreに追加済み）
- データベースのデータは`docker-compose down -v`で削除されます（`-v`オプションでボリュームも削除）
- 本番環境では、必ず`.env`ファイルの設定を適切に変更してください
