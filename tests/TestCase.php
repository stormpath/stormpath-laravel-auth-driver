<?php

class TestCase extends \PHPUnit_Framework_TestCase
{
    const STORMPATH_KEY_FILE_LOCATION = 'STORMPATH_KEY_FILE_LOCATION';
    const STORMPATH_ID = 'STORMPATH_ID';
    const STORMPATH_SECRET = 'STORMPATH_SECRET';
    const BASE_URL = 'STORMPATH_BASE_URL';
    protected static $client;

    public static function setUpBeforeClass()
    {
        if (self::$client)
        {
            return;
        }

        $apiKeyProperties = null;
        $apiKeyFileLocation = null;
        if (array_key_exists(self::STORMPATH_KEY_FILE_LOCATION, $_SERVER) or array_key_exists(self::STORMPATH_KEY_FILE_LOCATION, $_ENV))
        {
            $apiKeyFileLocation = array_key_exists(self::STORMPATH_KEY_FILE_LOCATION, $_SERVER) ?
                $_SERVER[self::STORMPATH_KEY_FILE_LOCATION] :
                $_ENV[self::STORMPATH_KEY_FILE_LOCATION];

        } elseif ((array_key_exists(self::STORMPATH_ID, $_SERVER) or array_key_exists(self::STORMPATH_ID, $_ENV))
            and (array_key_exists(self::STORMPATH_SECRET, $_SERVER) or array_key_exists(self::STORMPATH_SECRET, $_ENV)))
        {
            $apiKeyId = array_key_exists(self::STORMPATH_ID, $_SERVER) ?
                $_SERVER[self::STORMPATH_ID] :
                $_ENV[self::STORMPATH_ID];

            $apiKeySecret = array_key_exists(self::STORMPATH_SECRET, $_SERVER) ?
                $_SERVER[self::STORMPATH_SECRET] :
                $_ENV[self::STORMPATH_SECRET];

            $apiKeyProperties = "apiKey.id=$apiKeyId\napiKey.secret=$apiKeySecret";
        }
        else
        {
            $message = "The '" . self::STORMPATH_KEY_FILE_LOCATION . "' environment variable needs to be set before running the tests.\n" .
                "Alternatively, you can set the '" .self::STORMPATH_ID . "' and '" .self::STORMPATH_SECRET . "' environment " .
                "variables to make the tests run.";
            throw new \InvalidArgumentException($message);
        }

        $baseUrl = '';
        if (array_key_exists(self::BASE_URL, $_SERVER) or array_key_exists(self::BASE_URL, $_ENV))
        {
            $baseUrl = $_SERVER[self::BASE_URL] ?: $_ENV[self::BASE_URL];
        }

        \Stormpath\Client::$apiKeyFileLocation = $apiKeyFileLocation;
        \Stormpath\Client::$apiKeyProperties = $apiKeyProperties;
        \Stormpath\Client::$baseUrl = $baseUrl;
        \Stormpath\Client::$cacheManager = 'Array';
        \Stormpath\Client::$integration = 'stormpath-laravel-auth-driver/testing';

        self::$client = \Stormpath\Client::getInstance();
    }

    protected static function createResource($parentHref, \Stormpath\Resource\Resource $resource, array $options = array())
    {

        if (!(strpos($parentHref, '/') === 0))
        {
            $parentHref = '/' . $parentHref;
        }

        $resource = self::$client->dataStore->create($parentHref, $resource, get_class($resource), $options);
        return $resource;
    }

}