<?php

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

class CustomDataStub
{
    public $rememberToken = '111';
    public function __construct()
    {
        $this->rememberToken = '111';
    }
    public function save() {}
}