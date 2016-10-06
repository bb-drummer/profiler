<?php
use Profiler\Writer\CsvTable;
use Profiler\Profiler;

/**
 * CsvTable test case.
 */
class CsvTableTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Profiler
     */
    private $profiler;
    
    /**
     *
     * @var CsvTable
     */
    private $csvTable;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() 
    {
        parent::setUp();
        
        $this->profiler = Profiler::getInstance();
        $this->csvTable = new CsvTable($this->profiler);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() 
    {
        $this->csvTable = null;
        
        parent::tearDown();
    }
    
    /**
     * Tests CsvTable->get()
     */
    public function testGet() 
    {
        
        // create a checkpoint to display output for...
        $checkpoint = $this->profiler->start('my test-checkpoint');
        $j = 0; for ($i = 0; $i< 1000; $i++) { $j += $i; 
        } // do something, aka consume some memory for some time...
        $checkpoint->stop();
        
        $testOutput = $this->csvTable->get();
        
        $this->assertContains('my test-checkpoint', $testOutput);
        
    }
}

