<?php

namespace Stormpath;

use Illuminate\Contracts\Auth\Authenticatable;
use Stormpath\Resource\Account;

class StormpathUser implements Authenticatable
{

    /**
     * @var Account
     */
    private $account;

    /**
     * @param Account $account
     * @codeCoverageIgnore
     */
    public function __construct(Account $account)
    {

        $this->account = $account;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     * @since 0.1.0
     */
    public function getAuthIdentifier()
    {
        return $this->account->getHref();
    }

    /**
     * For security, this method is not provided in the Stormpath
     * auth driver.  This method will always return false and
     * will not be set up to return the password for users.
     *
     * @return false
     * @since 0.1.0
     */
    public function getAuthPassword()
    {
        return false;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     * @since 0.1.0
     */
    public function getRememberToken()
    {
        $tokenName = $this->getRememberTokenName();
        $cd = $this->account->customData;
        return $cd->$tokenName;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     * @since 0.1.0
     */
    public function setRememberToken($value)
    {
        $cd = $this->account->customData;
        $tokenName = $this->getRememberTokenName();
        $cd->$tokenName = $value;
        $cd->save();
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     * @since 0.1.0
     */
    public function getRememberTokenName()
    {
        return 'rememberToken';
    }

    /**
     * Dynamically access the user's account
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->account->$key;
    }

    /**
     * Dynamically set an attribute on the user.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->account->$key = $value;
    }

    /**
     * Dynamically check if a value is set on the user.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->account->$key);
    }

    /**
     * Dynamically unset a value on the user.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->account->$key);
    }
}