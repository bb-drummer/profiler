<?php
use Profiler\Checkpoint;
use Profiler\Profiler;

/**
 * Checkpoint test case.
 */
class CheckpointTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var Checkpoint
     */
    private $checkPoint;
    

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        
        parent::setUp();
        
        $this->checkPoint = new Checkpoint("my checkpoint", 1);
        
        $testTitle = $this->checkPoint->title;
        $this->assertEquals("my checkpoint", $testTitle);
        
        $testStartTime = $this->checkPoint->startTime;
        $this->assertGreaterThan(0, $testStartTime);
        
        $testStartMem = $this->checkPoint->startMemory;
        $this->assertGreaterThan(0, $testStartMem);
        
    }
    
    /**
     * Tests Checkpoint->stop()
     */
    public function testStop()
    {
        
        $this->checkPoint->stop();

        $testStartTime = $this->checkPoint->startTime;
        $testStopTime = $this->checkPoint->stopTime;
        $this->assertGreaterThan($testStartTime, $testStopTime);

        $testStartMem = $this->checkPoint->startMemory;
        $testStopMem = $this->checkPoint->stopMemory;
        $this->assertGreaterThan($testStartMem, $testStopMem);
        
    }
    
    /**
     * Tests Checkpoint->stop() returns null if in-active
     */
    public function testStopReturnsNull()
    {
        
        // stop this checkpoint, so active is set to false
        $this->checkPoint->stop();
        
        $test = $this->checkPoint->stop();
        $this->assertNull($test);
        
    }
    
}

