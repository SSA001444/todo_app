<?php

namespace Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;


class LoginUserTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function testLogin(): void
    {
        $user = User::factory()->create([
            'username' => 'SA',
            'email' => 'test@exmaple.com',
            'password' => bcrypt('password'),
            'is_email_verified' => 1,
        ]);

        $user = User::where('email', 'test@exmaple.com')->first();


        $response = $this->post('/authenticate', [
            'identity' => 'SA',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

    }
}


