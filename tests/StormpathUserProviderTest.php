<?php

use Mockery as m;

class StormpathUserProviderTest extends TestCase
{
    private static $application;

    private static $account;

    private static $provider;


    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$application = \Stormpath\Resource\Application::instantiate(array('name' => microtime().'ApplicationTest', 'description' => 'Description of Main App', 'status' => 'enabled'));
        self::createResource(\Stormpath\Resource\Application::PATH, self::$application, array('createDirectory' => true));

        self::$account = \Stormpath\Resource\Account::instantiate(array('givenName' => 'Account Name',
            'middleName' => 'Middle Name',
            'surname' => 'Surname',
            'username' => 'username'.time().microtime(),
            'email' => 'username'.time().microtime().'@unknown123.kot',
            'password' => 'superP4ss'));
        self::$application->createAccount(self::$account);

        self::$provider = new \Stormpath\StormpathUserProvider(self::$client, self::$application);

    }

    /**
     * @test
     */
    public function it_retrieves_by_id_and_returns_user()
    {

        $user = self::$provider->retrieveById(self::$account->href);

        $this->assertInstanceOf('Stormpath\StormpathUser', $user);
    }

    /**
     * @test
     */
    public function it_retrieves_by_credentials_and_returns_user()
    {
        $user = self::$provider->retrieveByCredentials([
            'email'=>self::$account->email,
            'password'=>'superP4ss'
        ]);


        $this->assertInstanceOf('Stormpath\StormpathUser', $user);
    }

    /**
     * @test
     */
    public function it_retrieves_by_credentials_and_returns_null_if_account_not_found()
    {
        $user = self::$provider->retrieveByCredentials(['email' => 'test@test.com', 'password' => 'foo']);

        $this->assertNull($user);
    }

    /**
     * @test
     */
    public function it_validates_credentials_and_returns_true_if_valid()
    {
        $user = self::$provider->retrieveByCredentials([
            'email'=>self::$account->email,
            'password'=>'superP4ss'
        ]);

        $valid = self::$provider->validateCredentials(
            $user,
            [
                'email' => self::$account->email,
                'password' => 'superP4ss'
            ]
        );

        $this->assertTrue($valid);
    }

    /**
     * @test
     */
    public function it_validates_credentials_and_returns_false_if_not_valid()
    {
        $user = self::$provider->retrieveByCredentials([
            'email'=>self::$account->email,
            'password'=>'superP4ss'
        ]);

        $valid = self::$provider->validateCredentials(
            $user,
            [
                'email' => 'test@email.com',
                'password' => 'superP4ss'
            ]
        );
        $this->assertFalse($valid);

    }

    /**
     * @test
     */
    public function it_validates_credentials_and_returns_false_if_exception_is_thrown()
    {
        $application = m::mock('Stormpath\\Resource\\Application')->makePartial();
        $application->__construct();
        $application->shouldReceive('authenticate')->andThrow('Exception', 'Some Exception');


        $user = self::$provider->retrieveByCredentials([
            'email'=>self::$account->email,
            'password'=>'superP4ss'
        ]);

        $valid = self::$provider->validateCredentials(
            $user,
            [
                'email' => 'test@email.com',
                'password' => 'superP4ss'
            ]
        );

        $this->assertFalse($valid);

    }

    /**
     * @test
     */
    public function it_can_retrieve_a_user_by_remember_token()
    {
        $user = self::$provider->retrieveByCredentials([
            'email'=>self::$account->email,
            'password'=>'superP4ss'
        ]);

        self::$provider->updateRememberToken($user, '1234');

        $userToTest = self::$provider->retrieveByToken($user->getAuthIdentifier(), '1234');

        $this->assertInstanceOf('Stormpath\StormpathUser', $userToTest);
    }

    /**
     * @test
     */
    public function it_returns_null_if_user_not_found_with_token()
    {
        $user = self::$provider->retrieveByCredentials([
            'email'=>self::$account->email,
            'password'=>'superP4ss'
        ]);

        self::$provider->updateRememberToken($user, '1234');

        $userToTest = self::$provider->retrieveByToken($user->getAuthIdentifier(), '2345');

        $this->assertNull($userToTest);
    }

    /**
     * @test
     */
    public function it_can_update_remember_token()
    {
        $user = self::$provider->retrieveByCredentials([
            'email'=>self::$account->email,
            'password'=>'superP4ss'
        ]);

        self::$provider->updateRememberToken($user, '1234');

        $userToTest = self::$provider->retrieveByToken($user->getAuthIdentifier(), '1234');

        $this->assertInstanceOf('Stormpath\StormpathUser', $userToTest);

        self::$provider->updateRememberToken($user, '1928374');

        $userToTest2 = self::$provider->retrieveByToken($user->getAuthIdentifier(), '1928374');

        $this->assertInstanceOf('Stormpath\StormpathUser', $userToTest2);

    }

    public static function tearDownAfterClass()
    {

        $accountStoreMappings = self::$application->accountStoreMappings;

        if ($accountStoreMappings)
        {
            foreach($accountStoreMappings as $asm)
            {
                $accountStore = $asm->accountStore;
                $asm->delete();
                $accountStore->delete();
            }
        }

        self::$application->delete();

        self::$client = null;
        self::$application = null;
        self::$account = null;

        parent::tearDownAfterClass();
    }

}