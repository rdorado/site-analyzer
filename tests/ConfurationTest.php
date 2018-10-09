<?php
include '..\Configuration.php';

final class ConfigurationTest extends PHPUnit_Framework_TestCase
{


    /**
     * @test
     * @covers Configuration::::__construct()
     */
    public function testLoadConfiguration()
    {
        $config = new Configuration("site-analyzer.ini");
        $config
    }


}
