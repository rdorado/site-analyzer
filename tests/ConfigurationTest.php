<?php
use PHPUnit\Framework\TestCase;

include 'src/Configuration.php';

final class ConfigurationTest extends TestCase
{


    /**
     * @test
     * @covers Configuration::::__construct()
     */
    public function testLoadConfiguration()
    {
        $config = new Configuration("site-analyzer.ini", FALSE);

        /* Test table Name */
        $val = $config->getCountTableName(); 
        $this->assertNotNull($val);


    }


    /**
     * @test
     * @covers Configuration::::__construct()
     */
    public function testLoadConfigurationWithPDO()
    {
        $config = new Configuration("site-analyzer.ini", TRUE);
        
        /*  */
        $val = $config->getCountTableName();
        $this->assertNull(  ); 
    }


}
