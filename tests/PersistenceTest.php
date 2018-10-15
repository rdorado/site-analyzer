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
     * Tests Persistence::deleteDatabase()
     */
    public function testDeleteDatabase()
    {
        $pdo = Persistence::getPDO($this->configuration);
        $resp = Persistence::deleteDatabase($pdo, $this->configuration);
        $this->assertTrue($resp);
    }
    


    /**
     * Tests Persistence::crateDatabase()
     */
    public function testCrateDatabase()
    {
        // TODO Auto-generated PersistenceTest::testCrateDatabase()
        $this->markTestIncomplete("crateDatabase test not implemented");

        Persistence::crateDatabase(/* parameters */);
    }


    /**
     * Tests Persistence::checkTables()
     */
    public function testCheckTables()
    {
        // TODO Auto-generated PersistenceTest::testCheckTables()
        $this->markTestIncomplete("checkTables test not implemented");

        Persistence::checkTables(/* parameters */);
    }

    /**
     * Tests Persistence::updateCount()
     */
    public function testUpdateCount()
    {
        // TODO Auto-generated PersistenceTest::testUpdateCount()
        $this->markTestIncomplete("updateCount test not implemented");

        Persistence::updateCount(/* parameters */);
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

