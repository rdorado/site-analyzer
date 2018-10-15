<?php
require_once 'src/Persistence.php';
require_once 'src/Configuration.php';

use PHPUnit\Framework\TestCase;

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
        
        try{
            $pdo = Persistence::getPDO($this->configuration);
            $tablesExist = Persistence::checkTables($pdo, $this->configuration);
            if($tablesExist){
                Persistence::deleteDatabase($pdo, $this->configuration);
            }
        }
        catch(Exception $e){
            print($e->getMessage());
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
        $resp = Persistence::crateDatabase($pdo, $this->configuration);
        $this->assertTrue($resp);
        
        $resp = Persistence::deleteDatabase($pdo, $this->configuration);
        $this->assertTrue($resp);
    }

    /**
     * Tests Persistence::checkTables()
     */
    public function testCheckTables()
    {
        $pdo = Persistence::getPDO($this->configuration);
        Persistence::crateDatabase($pdo, $this->configuration);
        
        $resp = Persistence::checkTables($pdo, $this->configuration);
        $this->assertTrue($resp);
    }

    /**
     * Tests Persistence::updateCount()
     */
    public function testUpdateCount()
    {
        $pdo = Persistence::getPDO($this->configuration);
        Persistence::crateDatabase($pdo, $this->configuration);
        
        Persistence::updateCount($pdo, $this->configuration);
        
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

    /**
     * Tests Persistence::getCountsById()
     */
    public function testGetCountsById()
    {
        // TODO Auto-generated PersistenceTest::testGetCountsById()
        $this->markTestIncomplete("getCountsById test not implemented");

        Persistence::getCountsById(/* parameters */);
    }
}

