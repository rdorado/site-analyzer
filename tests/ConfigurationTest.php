<?php
include 'src/Configuration.php';

use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{

    /**
     * @test
     * @covers Configuration::::__construct()
     */
    public function testLoadConfiguration()
    {
        $config = new Configuration("site-analyzer.ini", FALSE);

        /* Test get tables */
        $val = $config->getMainTableName();
        $this->assertNotNull($val);

        $val = $config->getOptionsTableName();
        $this->assertNotNull($val);
        
        $val = $config->getFromTableName();
        $this->assertNotNull($val);
    }


    /**
     * @test
     * @covers Configuration::::__construct()
     */
    public function testLoadConfigurationWithPDO()
    {
        $config = new Configuration("site-analyzer.ini", TRUE);

        $val = $config->getMainTableName();
        $this->assertNotNull($val);
        
        
        /*  */
    }


}
