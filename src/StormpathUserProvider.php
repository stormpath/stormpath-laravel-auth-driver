<?php

namespace Stormpath;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Stormpath\Resource\Account;

class StormpathUserProvider implements UserProvider {

    private $client;

    private $application;

    public function __construct($client, $application)
    {
        $this->client = $client;
        $this->application = $application;
    }
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $account = Account::get($identifier);
        return new StormpathUser($account);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $account = Account::get($identifier);

        $customData = $account->customData;
        if(!$customData->rememberToken || $customData->rememberToken != $token)
            return null;

        return new StormpathUser($account);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $account = Account::get($user->getAuthIdentifier());
        $customData = $account->customData;
        $customData->rememberToken = $token;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        try {
            $result = $this->application->authenticate($credentials['email'], $credentials['password']);
            return new StormpathUser($result->account);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        try {
            $result = $this->application->authenticate($credentials['email'], $credentials['password']);
            return $result->account->getHref() == $user->getAuthIdentifier();
        } catch (\Exception $e) {
            return false;
        }


    }
}