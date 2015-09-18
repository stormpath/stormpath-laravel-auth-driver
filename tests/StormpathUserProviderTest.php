<?php

use Mockery as m;

class StormpathUserProviderTest extends PHPUnit_Framework_TestCase
{
    protected static $account;

    protected static $client;

    protected static $spUserProvider;

    public function setUp()
    {
        self::$account = m::mock('Stormpath\\Resource\\Account')->makePartial();
        self::$account->__construct();
        self::$client = m::mock('Stormpath\\Client');
        self::$spUserProvider = new Stormpath\StormpathUserProvider(self::$client, m::mock('Stormpath\\Resource\\Application'));
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @test
     * @cover Stormpath\StormpathUserProvider::retrieveByTokenId
     */
    public function it_retrieves_a_stormpath_user_by_id()
    {
        $account = m::mock('Stormpath\\Resource\\Account');
        self::$client->shouldReceive('get')->andReturn($account);
        $user = self::$spUserProvider ->retrieveById('123');

        $this->assertInstanceOf('Stormpath\\StormpathUser', $user);
        $this->assertInstanceOf('Illuminate\\Contracts\\Auth\\Authenticatable', $user);

    }


}
