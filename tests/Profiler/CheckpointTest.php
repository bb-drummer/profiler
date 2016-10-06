<?php
use Profiler\Checkpoint;
use Profiler\Checkpoint\Dummy;
use Profiler\Checkpoint\CheckpointException;

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
     * Tests Checkpoint->getInfo()
     */
    public function testGetInfo()
    {
        
        $testInfo = $this->checkPoint->getInfo();
        
        $this->assertArrayHasKey("title", $testInfo);
        $this->assertEquals("my checkpoint", $testInfo['title']);
        
        $this->assertArrayHasKey("startTime", $testInfo);
        $this->assertGreaterThan(0, $testInfo['startTime']);
        
        $this->assertArrayHasKey("startMemory", $testInfo);
        $this->assertGreaterThan(0, $testInfo['startMemory']);
        
        $this->assertArrayHasKey("stopTime", $testInfo);
        $this->assertNull($testInfo['stopTime']);
        
        $this->assertArrayHasKey("stopMemory", $testInfo);
        $this->assertNull($testInfo['stopMemory']);
        
        $this->assertArrayHasKey("depth", $testInfo);
        $this->assertEquals(1, $testInfo['depth']);
        
    }
    
    /**
     * Tests magic Checkpoint->__get($name)
     */
    public function testMagicGet()
    {
        
        $testTitle = $this->checkPoint->title;
        $this->assertEquals("my checkpoint", $testTitle);
        
        $testDepth = $this->checkPoint->depth;
        $this->assertEquals(1, $testDepth);
        
        $testStartTime = $this->checkPoint->startTime;
        $this->assertGreaterThan(0, $testStartTime);
        
        $testStartMemory = $this->checkPoint->startMemory;
        $this->assertGreaterThan(0, $testStartMemory);
        
        $testStopTime = $this->checkPoint->stopTime;
        $this->assertNull($testStopTime);
        
        $testStopMemory = $this->checkPoint->stopMemory;
        $this->assertNull($testStopMemory);
        
    }
    
    /**
     * Tests magic Checkpoint->__get($name) throw exception on unknown info key
     *
     * @expectedException Profiler\Checkpoint\CheckpointException
     */
    public function testMagicGetThrowsExceptionOnUnknownKey()
    {
        
        $test = $this->checkPoint->unknownInfoKey;
        
    }
    
    /**
     * Tests Checkpoint->stop()
     */
    public function testStop()
    {
        
        $this->checkPoint->stop();
        
        $testActive = $this->checkPoint->isActive();
        $this->assertFalse($testActive);

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
        
        $testActive = $this->checkPoint->isActive();
        $this->assertFalse($testActive);
        
        $test = $this->checkPoint->stop();
        $this->assertNull($test);
        
    }
    
    /**
     * Tests Checkpoint->stop() on 'Dummy' checkpoint does nothing
     */
    public function testStopOnDummyDoesNothing()
    {
        
        $testDummy = new Dummy('dummy checkpoint');
        $test = $testDummy->stop();
        $this->assertNull($test);
        
    }
    
    
    
}

