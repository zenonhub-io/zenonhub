<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered()
    {
        $response = $this->get('/auth/forgot-password');

        $response->assertStatus(200);
    }

//    public function test_password_can_be_reset()
//    {
//        $user = User::factory()->create();
//
//        $response = $this->actingAs($user)->post('/confirm-password', [
//            'password' => 'password',
//        ]);
//
//        $response->assertRedirect();
//        $response->assertSessionHasNoErrors();
//    }
}
