<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト1-1: 名前が未入力の場合、バリデーションメッセージが表示される
     */
    public function test_register_validation_name_required(): void
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /**
     * テスト1-2: メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_register_validation_email_required(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * テスト1-3: パスワードが8文字未満の場合、バリデーションメッセージが表示される
     */
    public function test_register_validation_password_min_length(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * テスト1-4: パスワードが一致しない場合、バリデーションメッセージが表示される
     */
    public function test_register_validation_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    /**
     * テスト1-5: パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_register_validation_password_required(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * テスト1-6: フォームに内容が入力されていた場合、データが正常に保存される
     */
    public function test_register_success(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // リダイレクトを確認（メール認証が必要な場合は/email/verifyにリダイレクトされる可能性がある）
        $response->assertStatus(302);

        // データベースにユーザーが保存されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
    }
}

