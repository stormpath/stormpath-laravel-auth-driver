<?php

class StormpathConfigTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_contains_all_values_needed()
    {
        $stormpathConfig = include(__DIR__.'/../src/config/Stormpath.php');

        $this->assertCount(3, $stormpathConfig);
        $this->assertArrayHasKey('id', $stormpathConfig);
        $this->assertArrayHasKey('secret', $stormpathConfig);
        $this->assertArrayHasKey('application', $stormpathConfig);

    }
    
}
