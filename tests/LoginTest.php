<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->visit('/login')
            ->see('Login')
            ->type('systemadmin@logistics.com', 'email')
            ->type('12345', 'password')
            ->press('Login')
            ->seePageIs('/home');
    }
}
