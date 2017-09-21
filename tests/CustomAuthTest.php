<?php

use TheLHC\CustomAuth\Tests\TestCase;
use TheLHC\CustomAuth\Tests\User;
use Illuminate\Support\Facades\Auth;

class CustomAuthTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        if (! $this->user) {
            $this->user = User::create([
                'email' => 'aaronmichaelmusic@gmail.com',
                'name' => 'Aaron kaz',
                'password' => "1234567890"
            ]);
        }
    }

    public function testCustomAuthUserProvider()
    {
        $auth = Auth::validate([
            'email' => $this->user->email,
            'password' => "1234567890"
        ]);

        $this->assertTrue($auth);
    }

    public function testCustomAuthAdminUser()
    {
        Auth::adminOnceUsingId($this->user->id);

        $this->assertEquals($this->user->id, Auth::adminUser()->id);
    }
}
