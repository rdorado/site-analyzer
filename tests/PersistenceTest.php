<?php
namespace SiteAnalyzer;

use PHPUnit\Framework\TestCase;
use Exception;
/**
 * Persistence test case.
 */
class PersistenceTest extends TestCase
{

    /**
     *
     * @var Configuration
     */
    private $configuration;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->configuration = new Configuration("site-analyzer.ini", FALSE);
        
        $pdo = Persistence::getPDO($this->configuration);
        try{
            Persistence::deleteDatabase($pdo, $this->configuration);
        }
        catch(Exception $e){
        }
    }
    
    
    /**
     * Tests Persistence::getPDO()
     */
    public function testGetPDO()
    {
        $pdo = Persistence::getPDO($this->configuration);
        $this->assertNotNull($pdo);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->persistence = null;
        parent::tearDown();
    }    


    /**
     * Tests Persistence::crateDatabase()
     */
    public function testCrateDeleteDatabase()
    {
        $pdo = Persistence::getPDO($this->configuration);
        try{
            Persistence::deleteDatabase($pdo, $this->configuration);
        }
        catch(Exception $e) {};
        
        $resp = Persistence::crateDatabase($pdo, $this->configuration);
        $this->assertTrue($resp);
        
        $resp = Persistence::checkTables($pdo, $this->configuration);
        $this->assertTrue($resp);
        
        $resp = Persistence::deleteDatabase($pdo, $this->configuration);
        $this->assertTrue($resp);
        
        $resp = Persistence::checkTables($pdo, $this->configuration);
        $this->assertFalse($resp);
    }

    /**
     * Tests Persistence::checkTables()
     */
    public function testCheckTables()
    {
        $pdo = Persistence::getPDO($this->configuration);
        try{ 
           Persistence::crateDatabase($pdo, $this->configuration);
        }
        catch(Exception $e) {};
        
        $resp = Persistence::checkHitTable($pdo, $this->configuration);
        $this->assertTrue($resp);
        
        $resp = Persistence::checkOptionsTable($pdo, $this->configuration);
        $this->assertTrue($resp);
        
        $resp = Persistence::checkOptionsTable($pdo, $this->configuration);
        $this->assertTrue($resp);
        
        $resp = Persistence::checkTables($pdo, $this->configuration);
        $this->assertTrue($resp);

        try{
            Persistence::deleteDatabase($pdo, $this->configuration);
        }
        catch(Exception $e) {};
        
        $resp = Persistence::checkHitTable($pdo, $this->configuration);
        $this->assertFalse($resp);
        
        $resp = Persistence::checkOptionsTable($pdo, $this->configuration);
        $this->assertFalse($resp);
        
        $resp = Persistence::checkOptionsTable($pdo, $this->configuration);
        $this->assertFalse($resp);
        
        $resp = Persistence::checkTables($pdo, $this->configuration);
        $this->assertFalse($resp);
        
    }

    /**
     * printTable() : use for development/debug purposes
     */
    public function printTable($data)
    {
        print("\n");
        foreach ($data as $row){
            print("'".join("','",$row)."'\n");
        }
    }
    
    
    /**
     * Tests Persistence::updateHits()
     */
    public function testUpdateHits()
    {
        $pdo = Persistence::getPDO($this->configuration);
        try{
            Persistence::deleteDatabase($pdo, $this->configuration);
        }
        catch(Exception $e) {};
        
        Persistence::crateDatabase($pdo, $this->configuration);         
        Persistence::updateCount($pdo, $this->configuration);
        $resp = Persistence::getAllHits($pdo, $this->configuration);
        
        $this->assertEquals(count($resp), 1);
        $this->assertEquals($resp[0][1], 1);

        $options = ["id"=>"Page 1"];
        Persistence::updateCount($pdo, $this->configuration, $options);
        $resp = Persistence::getAllHits($pdo, $this->configuration);
        
        $this->assertEquals(count($resp), 2);
        
        $options = ["id"=>"Page 2"];
        Persistence::updateCount($pdo, $this->configuration, $options);
        Persistence::updateCount($pdo, $this->configuration);
        $options = ["id"=>"Page 1"];
        Persistence::updateCount($pdo, $this->configuration, $options);
        $resp = Persistence::getAllHits($pdo, $this->configuration);
        
        $this->assertEquals(count($resp), 3);
        foreach($resp as $row){
            if($row[0]=="Page 1") $this->assertEquals($row[1], 2);
            else if($row[0]=="Page 2") $this->assertEquals($row[1], 1);
            else if($row[0]=="No Info") $this->assertEquals($row[1], 2);
        }        
    }

    
    /**
     * Tests Persistence::updateHits()
     */
    public function testUpdateUrls()
    {
        $pdo = Persistence::getPDO($this->configuration);
        try{
            Persistence::deleteDatabase($pdo, $this->configuration);
        }
        catch(Exception $e) {};
        
        Persistence::crateDatabase($pdo, $this->configuration);
        Persistence::updateCount($pdo, $this->configuration);
        $resp = Persistence::findUrls($pdo, $this->configuration);        
        $this->assertEquals(count($resp), 2);
        
        $insert_options = ['url' => 'http://test.test'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        $find_options = ['url' => 'http://test.test'];
        $resp = Persistence::findUrls($pdo, $this->configuration, $find_options);
        $this->assertEquals(count($resp), 1);        
        
        $insert_options = ['url' => 'http://test.test', 'id' => 'No Info'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        Persistence::updateCount($pdo, $this->configuration);
        $find_options = ['url' => 'http://test.test'];
        $resp = Persistence::findUrls($pdo, $this->configuration, $find_options);
        $this->assertEquals(count($resp), 2);
        
        $resp = Persistence::findUrls($pdo, $this->configuration);
        $this->assertEquals(count($resp), 4);
        
    }
    

    /**
     * Tests Persistence::updateHits()
     */
    public function testUpdateTimeAndUser()
    {
        $pdo = Persistence::getPDO($this->configuration);
        try{
            Persistence::deleteDatabase($pdo, $this->configuration);
        }
        catch(Exception $e) {};
        
        
        Persistence::crateDatabase($pdo, $this->configuration);

        $insert_options = ['url' => 'http://test.test', 'user' => '1'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
                
        Persistence::updateCount($pdo, $this->configuration);
        $resp = Persistence::findIdByTimeUser($pdo, $this->configuration);        
        $this->assertEquals(count($resp), 2);
                
        $find_options = ['from' => strtotime("1/8/2018")];
        $resp = Persistence::findIdByTimeUser($pdo, $this->configuration, $find_options);
        $this->assertEquals(count($resp), 2);
        
        $time1 = time();
        $insert_options = ['id' => 'Page 2', 'url' => 'http://page2.test', 'user' => '2'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        $insert_options = ['id' => 'Page 1', 'url' => 'http://page1.test', 'user' => '2'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        $insert_options = ['id' => 'Page 2', 'url' => 'http://page2.test', 'user' => '1'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        $insert_options = ['id' => 'Page 2', 'url' => 'http://page2.test', 'user' => '2'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        $insert_options = ['id' => 'Page 1', 'url' => 'http://page1.test', 'user' => '2'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        
        $time2 = time();
        $insert_options = ['id' => 'Page 1', 'url' => 'http://page1.test', 'user' => '1'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        $insert_options = ['id' => 'Page 2', 'url' => 'http://page2.test', 'user' => '1'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        $insert_options = ['id' => 'Page 1', 'url' => 'http://page1.test', 'user' => '1'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        
        $resp = Persistence::findIdByTimeUser($pdo, $this->configuration);
        $this->assertEquals(count($resp), 10);        
        
        $find_options = ['from' => $time1, 'user' => 1];
        $resp = Persistence::findIdByTimeUser($pdo, $this->configuration, $find_options);
        //$this->assertEquals(count($resp), 4);
        
        
        $find_options = ['user' => '2'];
        $resp = Persistence::findIdByTimeUser($pdo, $this->configuration, $find_options);
        $this->assertEquals(count($resp), 4);
        
        
        $find_options = ['to'=> $time1, 'user' => 1];
        $resp = Persistence::findIdByTimeUser($pdo, $this->configuration, $find_options);
        //$this->printTable($resp);
        //$this->assertEquals(count($resp), 2);
        
    }
    

    
    /**
     * Tests Persistence::updateHits()
     */
    public function testInsertFrom()
    {
        $pdo = Persistence::getPDO($this->configuration);
        try{
            Persistence::deleteDatabase($pdo, $this->configuration);
        }
        catch(Exception $e) {};
        
        Persistence::crateDatabase($pdo, $this->configuration);
    
        $insert_options = ['url' => 'http://test.test', 'id' => 'No Info'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        Persistence::updateCount($pdo, $this->configuration);
                
        $resp = Persistence::findByFrom($pdo, $this->configuration);
        $this->printTable($resp);
        $this->assertEquals(count($resp), 1);
        
        $insert_options = ['from_id' => 'http://test.test', 'id' => 'Test'];
        Persistence::updateCount($pdo, $this->configuration, $insert_options);
        $resp = Persistence::findByFrom($pdo, $this->configuration);
        $this->assertEquals(count($resp), 2);
        
        $find_options = ['url' => 'http://test.test'];
        $resp = Persistence::findByFrom($pdo, $this->configuration, $find_options);
        $this->assertEquals(count($resp), 1);
        
        $find_options = ['id' => 'Test'];
        $resp = Persistence::findByFrom($pdo, $this->configuration, $find_options);
        $this->assertEquals(count($resp), 1);

        $find_options = [];
        $resp = Persistence::findByFrom($pdo, $this->configuration);
        $this->assertEquals(count($resp), 2);
        
        $this->printTable($resp);
        /* */
        
    }
    
    
    /**
     * Tests Persistence::getCounts()
     */
    public function testGetCounts()
    {
        // TODO Auto-generated PersistenceTest::testGetCounts()
        $this->markTestIncomplete("getCounts test not implemented");

        Persistence::getCounts(/* parameters */);
    }

}

