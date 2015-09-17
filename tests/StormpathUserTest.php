<?php

use Mockery as m;

class StormpathUserTest extends PHPUnit_Framework_TestCase
{
    protected static $account;

    protected static $spUser;

    public function setUp()
    {
        self::$account = m::mock('Stormpath\\Resource\\Account')->makePartial();
        self::$account->__construct();
        self::$spUser = new \Stormpath\StormpathUser(self::$account);
    }

    public function tearDown()
    {
        m::close();
        self::$account = null;
        self::$spUser = null;
    }

    /**
     * @test
     * @covers Stormpath\StormpathUser::getAuthIdentifier
     */
    public function it_will_return_auth_identifier_of_the_account()
    {
        self::$account->shouldReceive('getHref')->once()->andReturn('hello');

        $identifier = self::$spUser->getAuthIdentifier();

        $this->assertEquals('hello', $identifier, 'The getAuthIdentifier did not return correctly!');

    }

    /**
     * @test
     * @covers Stormpath\StormpathUser::getAuthPassword
     */
    public function it_will_return_false_if_get_auth_password_is_called()
    {
        $password = self::$spUser->getAuthPassword();

        $this->assertFalse($password);
    }

    /**
     * @test
     * @covers Stormpath\StormpathUser::getRememberTokenName
     */
    public function it_will_get_the_remember_token_name()
    {
        $tokenName = self::$spUser->getRememberTokenName();

        $this->assertEquals('rememberToken', $tokenName, 'The remember token name was not retrieved correctly!');
    }

    /**
     * @test
     * @covers Stormpath\StormpathUser::getRememberToken
     */
    public function it_will_get_remember_token_from_custom_data_of_the_account()
    {
        $customData = m::mock('Stormpath\\Resource\\CustomData');
        $customData->shouldReceive('getProperty')->with('rememberToken')->andReturn('token');
        self::$account->shouldReceive('getCustomData')->andReturn($customData);
        $this->assertEquals('token', self::$spUser->getRememberToken(), 'The remember token was not retrieved correctly!');
    }

    /**
     * @test
     * @covers Stormpath\StormpathUser::setRememberToken
     */
    public function it_will_set_remember_token_on_the_account()
    {
        $customData = m::mock('CustomDataStub');
        $customData->shouldReceive('save')->andReturnNull();
        self::$account->shouldReceive('getCustomData')->andReturn($customData);
        self::$spUser->setRememberToken('token123');
        $this->assertEquals('token123', self::$spUser->getRememberToken());

    }
}

class CustomDataStub
{
    public function save() {}
}