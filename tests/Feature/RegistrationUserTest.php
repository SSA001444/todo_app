<?php

namespace Feature;

use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationUserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testUserRegistration(): void
    {

        $response = $this->post('/store',[
            'username' => 'Joe',
            'email' => 'joe@exmp.com',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ]);

        $user = User::where('email', 'joe@exmp.com')->first();

        $this->assertFalse($user->hasVerifiedEmail());

        $verify = UserVerify::where('user_id', $user->id )->first();

        $token = $verify->token;

        $response = $this->get('/account/verify/'.$token);

        $user = User::where('email', 'joe@exmp.com')->first();

        $this->assertTrue($user->hasVerifiedEmail());

    }
}
