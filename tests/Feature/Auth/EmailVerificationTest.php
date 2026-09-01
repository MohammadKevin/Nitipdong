<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified_with_otp(): void
    {
        $user = User::factory()->unverified()->create([
            'otp_code'       => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post('/verify-email/submit', [
            'otp' => '123456',
        ]);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('home'));
    }

    public function test_email_can_be_verified_with_spaced_otp(): void
    {
        $user = User::factory()->unverified()->create([
            'otp_code'       => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post('/verify-email/submit', [
            'otp' => '123 456',
        ]);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('home'));
    }

    public function test_email_is_not_verified_with_invalid_otp(): void
    {
        $user = User::factory()->unverified()->create([
            'otp_code'       => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post('/verify-email/submit', [
            'otp' => '654321',
        ]);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $response->assertSessionHasErrors('otp');
    }
}
