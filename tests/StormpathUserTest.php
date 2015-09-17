<?php

use Mockery as m;

class StormpathUserTest extends PHPUnit_Framework_TestCase
{
    protected static $account;

    protected static $spUser;

    public static function setUpBeforeClass()
    {
        self::$account = m::mock('Stormpath\\Resource\\Account');
        self::$spUser = new \Stormpath\StormpathUser(self::$account);
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function it_will_return_auth_identifier_of_the_account()
    {
        self::$account->shouldReceive('getHref')->once()->andReturn('hello');

        $identifier = self::$spUser->getAuthIdentifier();

        $this->assertEquals('hello', $identifier, 'The getAuthIdentifier did not return correctly!');

    }

    /**
     * @test
     */
    public function it_will_return_false_if_get_auth_password_is_called()
    {
        $password = self::$spUser->getAuthPassword();

        $this->assertFalse($password);
    }

    /**
     * @test
     */
    public function it_will_get_the_remember_token_name()
    {
        $tokenName = self::$spUser->getRememberTokenName();

        $this->assertEquals('rememberToken', $tokenName, 'The remember token name was not retrieved correctly!');
    }

}