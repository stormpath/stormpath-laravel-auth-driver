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
    }

    public function tearDown()
    {
        m::close();
        self::$client = null;
        self::$application = null;
        self::$account = null;
    }
    
    /**
     * @test
     */
    public function it_retrieves_by_id_and_returns_user()
    {
        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        self::$client->shouldReceive('get')->with('accounts/1', Stormpath\Stormpath::ACCOUNT)->once()->andReturn(self::$account);
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

    /**
     * @test
     */
    public function it_validates_credentials_and_returns_false_if_exception_is_thrown()
    {
        $authResult = m::mock('Stormpath\\Resource\\AuthenticationResult');
        self::$application->shouldReceive('authenticate')->andThrow('Exception', 'Some Exception');

        $user = m::mock('Illuminate\Contracts\Auth\Authenticatable');
        $user->shouldReceive('getAuthIdentifier')->andReturnNull();

        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        $valid = $provider->validateCredentials($user, ['email' => 'test@test.com', 'password' => 'foo']);

        $this->assertFalse($valid);

    }

    /**
     * @test
     */
    public function it_can_retrieve_a_user_by_remember_token()
    {
        $customData = m::mock('Stormpath\\Resource\\CustomData')->makePartial();
        $customData->__construct();
        $customData->shouldReceive('getProperty')->with('rememberToken')->andReturn('456');

        self::$client->shouldReceive('get')->with('accounts/123', Stormpath\Stormpath::ACCOUNT)->andReturn(self::$account);
        self::$account->shouldReceive('getCustomData')->andReturn($customData);

        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        $user = $provider->retrieveByToken('123', '456');

        $this->assertInstanceOf('Stormpath\StormpathUser', $user);
    }

    /**
     * @test
     */
    public function it_returns_null_if_user_not_found_with_token()
    {
        $customData = m::mock('Stormpath\\Resource\\CustomData')->makePartial();
        $customData->__construct();
        $customData->shouldReceive('getProperty')->with('rememberToken')->andReturnNull();

        self::$client->shouldReceive('get')->with('accounts/123', Stormpath\Stormpath::ACCOUNT)->andReturn(self::$account);
        self::$account->shouldReceive('getCustomData')->andReturn($customData);

        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);
        $user = $provider->retrieveByToken('123', '456');

        $this->assertNull($user);
    }

    /**
     * @test
     */
    public function it_can_update_remember_token()
    {
        self::$client->shouldReceive('get')->andReturn(self::$account);
        $user = m::mock('Illuminate\Contracts\Auth\Authenticatable');
        $user->shouldReceive('getAuthIdentifier')->andReturnNull();

        $customData = m::mock('Stormpath\\Resource\\CustomData')->makePartial();
        $customData->shouldAllowMockingProtectedMethods();
        $customData->__construct();
        $customData->shouldReceive('getProperty')->with('rememberToken');

        $provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);

        $provider->updateRememberToken($user, 'tokenTest');
        
    }





}



