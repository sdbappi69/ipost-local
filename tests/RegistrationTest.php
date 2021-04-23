<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->visit('/register')
            ->see('Confirm Password');
    }

    /*
     * Test Registration
     *
     * @return void
     */
//    public function testRegistration()
//    {
//        $this->visit('/register')
//            ->see('Confirm Password')
//            ->type('John Doe', 'name')
//            ->type('mail@johndoe.me', 'email')
//            ->type('123456', 'password')
//            ->type('123456', 'password_confirmation')
//            ->press('Register')
//            ->seePageIs('/home')
//            ->see('Dashboard');
//    }

}
