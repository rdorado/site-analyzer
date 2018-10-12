<?php
include 'src/Configuration.php';

final class ConfigurationTest extends PHPUnit_Framework_TestCase
{


    /**
     * @test
     * @covers Configuration::::__construct()
     */
    public function testLoadConfiguration()
    {
        $config = new Configuration("site-analyzer.ini", FALSE);
        $val = $config->getCountTableName() 
        //$this->assertNull(  ); 
    }


}
