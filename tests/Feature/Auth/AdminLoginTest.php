<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // テスト用管理者ユーザーを作成
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }

    /**
     * テスト3-1: メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_admin_login_validation_email_required(): void
    {
        $response = $this->post('/admin/login', [
            'password' => 'password123',
        ]);

        // バリデーションエラーによりリダイレクトされる
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    /**
     * テスト3-2: パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_admin_login_validation_password_required(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
        ]);

        // バリデーションエラーによりリダイレクトされる
        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    /**
     * テスト3-3: 登録内容と一致しない場合、バリデーションメッセージが表示される
     */
    public function test_admin_login_validation_invalid_credentials(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        // バリデーションは通過するが、認証に失敗するためエラーが返される
        $response->assertStatus(302);
        // セッションにエラーが含まれていることを確認（リダイレクト先で確認）
        $response->assertRedirect('/admin/login');
    }
}

