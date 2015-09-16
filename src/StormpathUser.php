<?php

namespace Stormpath;

use Illuminate\Contracts\Auth\Authenticatable;

class StormpathUser implements Authenticatable
{

    private $account;

    public function __construct($account)
    {

        $this->account = $account;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
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
     */
    public function getAuthPassword()
    {
        return false;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
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
     */
    public function getRememberTokenName()
    {
        return 'rememberToken';
    }

    public function __get($property)
    {
        return $this->account->getProperty($property);
    }
}