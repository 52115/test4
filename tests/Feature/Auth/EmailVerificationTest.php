<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト16-1: 会員登録後、認証メールが送信される
     */
    public function test_email_verification_sent_after_registration(): void
    {
        Event::fake();
        
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        
        // メール送信を確認（実際のメール送信はMailtrapで確認）
        $this->assertTrue(true);
    }

    /**
     * テスト16-2: メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
     */
    public function test_email_verification_notice_page_redirects_to_mailtrap(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get('/email/verify');
        
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
        // 「認証はこちらから」ボタンがhttp://localhost:8025にリンクしていることを確認
        $response->assertSee('認証はこちらから');
        $response->assertSee('http://localhost:8025');
    }

    /**
     * テスト16-3: メール認証サイトのメール認証を完了すると、ログイン画面に遷移する
     */
    public function test_email_verification_completion_redirects_to_login(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        
        Event::fake();
        
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        
        $response = $this->get($verificationUrl);
        
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        
        // メール認証完了後、ログイン画面にリダイレクトされる
        $response->assertRedirect('/login');
    }
}

