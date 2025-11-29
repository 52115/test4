<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // テスト用ユーザーを作成
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }

    /**
     * テスト2-1: メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_login_validation_email_required(): void
    {
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * テスト2-2: パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_login_validation_password_required(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * テスト2-3: 登録内容と一致しない場合、バリデーションメッセージが表示される
     */
    public function test_login_validation_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors();
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }
}

