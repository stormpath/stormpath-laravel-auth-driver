<?php

use Mockery as m;

class StormpathUserProviderTest extends PHPUnit_Framework_TestCase
{
    protected static $client;

    protected static $application;

    protected static $account;


    public function setUp()
    {
        self::$client = m::mock('Stormpath\\Client');
        self::$application = m::mock('Stormpath\\Resource\\Application')->makePartial();
        self::$application->__construct();
        self::$account = m::mock('Stormpath\\Resource\\Account')->makePartial();
        self::$account->__construct();
        self::$account->href = '123';
    }

    public function tearDown()
    {
        m::close();
    }
    
    /**
     * @test
     */
    public function it_retrieves_by_id_and_returns_user()
    {
        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        self::$client->shouldReceive('get')->once()->andReturn(self::$account);
        $user = $provider->retrieveById(1);

        $this->assertInstanceOf('Stormpath\StormpathUser', $user);
    }

    /**
     * @test
     */
    public function it_retrieves_by_credentials_and_returns_user()
    {
        $authResult = m::mock('AthenticationResultStub');
        $authResult->account = self::$account;
        self::$application->shouldReceive('authenticate')->andReturn($authResult);


        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        $user = $provider->retrieveByCredentials(['email' => 'test@test.com', 'password' => 'foo']);

        $this->assertInstanceOf('Stormpath\StormpathUser', $user);
    }

    /**
     * @test
     */
    public function it_retrieves_by_credentials_and_returns_null_if_account_not_found()
    {
        $authResult = m::mock('AthenticationResultStub');
        $authResult->account = null;
        self::$application->shouldReceive('authenticate')->andReturn($authResult);


        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        $user = $provider->retrieveByCredentials(['email' => 'test@test.com', 'password' => 'foo']);

        $this->assertNull($user);
    }

    /**
     * @test
     */
    public function it_validates_credentials_and_returns_true_if_valid()
    {
        $authResult = m::mock('AthenticationResultStub');
        $authResult->account = m::mock('AccountStub')->makePartial();
        self::$application->shouldReceive('authenticate')->andReturn($authResult);

        $user = m::mock('Illuminate\Contracts\Auth\Authenticatable');
        $user->shouldReceive('getAuthIdentifier')->once()->andReturn('123');


        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        $valid = $provider->validateCredentials($user, ['email' => 'test@test.com', 'password' => 'foo']);

        $this->assertTrue($valid);
    }

    /**
     * @test
     */
    public function it_validates_credentials_and_returns_false_if_not_valid()
    {
        $authResult = m::mock('AthenticationResultStub');
        $authResult->account = m::mock('AccountStub')->makePartial();
        self::$application->shouldReceive('authenticate')->andReturn($authResult);

        $user = m::mock('Illuminate\Contracts\Auth\Authenticatable');
        $user->shouldReceive('getAuthIdentifier')->once()->andReturn('456');


        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        $valid = $provider->validateCredentials($user, ['email' => 'test@test.com', 'password' => 'foo']);

        $this->assertFalse($valid);
    }





}


class StormpathUserStub {}

class AthenticationResultStub
{
    public $account;
}

class AccountStub
{
    public function getHref()
    {
        return '123';
    }
}
