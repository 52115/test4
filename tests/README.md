# テスト実行ガイド

## テストの実行方法

### Dockerコンテナ内でテストを実行

```bash
# すべてのテストを実行
docker-compose exec app php artisan test

# 特定のテストクラスを実行
docker-compose exec app php artisan test tests/Feature/Auth/RegisterTest.php

# 特定のテストメソッドを実行
docker-compose exec app php artisan test --filter test_register_validation_name_required
```

### ローカル環境でテストを実行（Composerがインストールされている場合）

```bash
# すべてのテストを実行
./vendor/bin/phpunit

# 特定のテストクラスを実行
./vendor/bin/phpunit tests/Feature/Auth/RegisterTest.php
```

## テストケース一覧

### 1. 認証機能（一般ユーザー） - RegisterTest
- 名前が未入力の場合のバリデーション
- メールアドレスが未入力の場合のバリデーション
- パスワードが8文字未満の場合のバリデーション
- パスワードが一致しない場合のバリデーション
- パスワードが未入力の場合のバリデーション
- 正常な登録処理

### 2. ログイン認証機能（一般ユーザー） - LoginTest
- メールアドレスが未入力の場合のバリデーション
- パスワードが未入力の場合のバリデーション
- 登録内容と一致しない場合のバリデーション

### 3. ログイン認証機能（管理者） - AdminLoginTest
- メールアドレスが未入力の場合のバリデーション
- パスワードが未入力の場合のバリデーション
- 登録内容と一致しない場合のバリデーション

### 4-8. 勤怠打刻機能 - ClockControllerTest
- 日時取得機能
- ステータス確認機能（勤務外、出勤中、休憩中、退勤済）
- 出勤機能
- 休憩機能
- 退勤機能

### 9-11. 勤怠一覧・詳細・修正機能（一般ユーザー） - AttendanceControllerTest
- 勤怠一覧情報取得機能
- 勤怠詳細情報取得機能
- 勤怠詳細情報修正機能

### 12-13. 勤怠管理機能（管理者） - AdminAttendanceControllerTest
- 勤怠一覧情報取得機能
- 勤怠詳細情報取得・修正機能

### 14. ユーザー情報取得機能（管理者） - AdminStaffControllerTest
- スタッフ一覧表示
- 月次勤怠情報表示

### 15. 修正申請管理機能（管理者） - AdminStampCorrectionRequestControllerTest
- 承認待ち・承認済み申請一覧
- 修正申請の承認処理

### 16. メール認証機能 - EmailVerificationTest
- 認証メール送信
- メール認証完了

## 注意事項

- テスト実行前に、データベースが正しく設定されていることを確認してください
- テストは`RefreshDatabase`トレイトを使用しているため、各テスト実行後にデータベースがリセットされます
- 実際のコントローラーやビューの実装に合わせて、テストを調整する必要がある場合があります

