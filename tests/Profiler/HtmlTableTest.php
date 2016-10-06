<?php
use Profiler\Writer\HtmlTable;
use Profiler\Profiler;

/**
 * HtmlTable test case.
 */
class HtmlTableTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Profiler
     */
    private $profiler;
    
    /**
     *
     * @var HtmlTable
     */
    private $htmlTable;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() 
    {
        parent::setUp();
        
        $this->profiler = Profiler::getInstance();
        $this->htmlTable = new HtmlTable($this->profiler);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() 
    {
        $this->htmlTable = null;
        
        parent::tearDown();
    }
    
    /**
     * Tests HtmlTable->get()
     */
    public function testGet() 
    {
        
        // create a checkpoint to display output for...
        $checkpoint = $this->profiler->start('my test-checkpoint');
        $j = 0; for ($i = 0; $i< 1000; $i++) { $j += $i; 
        } // do something, aka consume some memory for some time...
        $checkpoint->stop();
        
        $testOutput = $this->htmlTable->get();
        
        $this->assertContains('my test-checkpoint', $testOutput);
    }
}

