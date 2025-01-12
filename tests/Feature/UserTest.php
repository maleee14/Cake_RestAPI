<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'password' => '123123123'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'johndoe@gmail.com',
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/users', [
            'name' => '',
            'email' => 'johndoe',
            'password' => '123123123'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }

    public function testEmailAlreadyRegisted()
    {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com',
            'password' => '123123123'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'email already registered'
                    ],
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'email' => 'johndoe@gmail.com',
            'password' => '123123123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'johndoe@gmail.com',
                ]
            ]);

        $user = User::where('email', 'johndoe@gmail.com')->first();
        self::assertNotNull($user->auth_token);
    }

    public function testLoginFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/users/login', [
            'email' => 'salah@gmail.com',
            'password' => '123123123'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'email or password wrong'
                    ]
                ]
            ]);
    }

    public function testProfileSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->get('/api/users/profile', [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'johndoe@gmail.com'
                ]
            ]);
    }

    public function testProfileInvalidToken()
    {
        $this->seed([UserSeeder::class]);
        $this->get(
            '/api/users/profile',
            [
                'Authorization' => 'salah'
            ]
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'invalid token'
                    ]
                ]
            ]);
    }

    public function testProfileUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->get(
            '/api/users/profile',
            []
        )->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('email', 'johndoe@gmail.com')->first();
        $this->patch('/api/users/profile', [
            'name' => 'John Morron'
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'John Morron',
                    'email' => 'johndoe@gmail.com'
                ]
            ]);

        $newUser = User::where('email', 'johndoe@gmail.com')->first();
        self::assertNotEquals($user->name, $newUser->name);
    }

    public function testUpdateEmailSuccess()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('name', 'John Doe')->first();
        $this->patch('/api/users/profile', [
            'email' => 'doejohn@gmail.com'
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'doejohn@gmail.com'
                ]
            ]);

        $newUser = User::where('name', 'John Doe')->first();
        self::assertNotEquals($user->email, $newUser->email);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $user = User::where('email', 'johndoe@gmail.com')->first();
        $this->patch('/api/users/profile', [
            'password' => 'ganti'
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'johndoe@gmail.com'
                ]
            ]);

        $newUser = User::where('email', 'johndoe@gmail.com')->first();
        self::assertNotEquals($user->password, $newUser->password);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->patch(
            '/api/users/profile',
            [
                'name' => 'mEGLgjGdTPkHXqXKhpeoRjBuyCLfKSdpNIGzGMFKKfkLriYsPSFpMgksBnezqgyjKKaBdoKOGdGMpgiVJLDEUCSEQgblGBDVuQMBRqCbr'
            ],
            [
                'Authorization' => 'token123'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field must not be greater than 100 characters.'
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);

        $user = User::where('name', 'John Doe')->first();
        self::assertNull($user->auth_token);
    }

    public function testLogoutFailed()
    {
        $this->delete(uri: '/api/users/logout', headers: [])
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testLogoutInvalid()
    {
        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'invalid token'
                    ]
                ]
            ]);
    }
}
