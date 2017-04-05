<?php

use TheLHC\CustomAuth\Tests\TestCase;
use TheLHC\CustomAuth\Tests\User;
use Illuminate\Support\Facades\Auth;

class CustomAuthTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $salt = hash(
          'sha512',
          substr(
            str_shuffle(
              str_repeat('0123456789abcdefghijklmnopqrstuvwxyz',5)
            ),
            0,
            5
          ).mt_rand(1,999999)*memory_get_usage(true)
        );
        $password = hash('sha512', "1234567890" . $salt);

        $this->user = User::create([
            'email' => 'aaronmichaelmusic@gmail.com',
            'name' => 'Aaron kaz',
            'salt' => $salt,
            'password' => $password
        ]);
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
        session()->put('adminid', $this->user->id);

        $this->assertEquals($this->user->id, Auth::adminUser()->id);
    }
}
