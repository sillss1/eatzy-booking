<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use App\Models\User;
use App\Mail\MailModel;

class PasswordResetTest extends TestCase
{
    // Use RefreshDatabase to ensure a clean state for each test
    use RefreshDatabase;

    public function test_password_reset_link_can_be_requested()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status', trans('passwords.sent'));

        Mail::assertSent(MailModel::class, function ($mail) use ($user) {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_password_can_be_reset_with_token()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        // Create a password reset token for the user
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHas('status', 'Your password has been reset! Please log in with your new password.');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }
}
