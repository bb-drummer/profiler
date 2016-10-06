<?php
use Profiler\Profiler;
use Profiler\Writer\HtmlTable;
use Profiler\Writer\CsvTable;

/**
 * Profiler test case.
 */
class ProfilerTest extends PHPUnit_Framework_TestCase
{
    
    /**
     *
     * @var Profiler
     */
    private $profiler;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() 
    {
        parent::setUp();
        
        $this->profiler = Profiler::getInstance();
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() 
    {
        $this->profiler = null;
        
        parent::tearDown();
    }
    
    /**
     * Tests Profiler::getInstance()
     */
    public function testGetInstance() 
    {

        Profiler::destroy();
        $profiler = Profiler::getInstance();
        $this->assertInstanceOf("Profiler\\Profiler", $profiler);
        
    }
    
    /**
     * Tests Profiler::getInstance() throws exception if non-array parameter is given
     * 
     * @expectedException Profiler\Exception
     */
    public function testGetInstanceThrowsExceptionOnInvalidParameter() 
    {
        
        Profiler::destroy();
        $profiler = Profiler::getInstance("invalid parameter");
        
    }
    
    /**
     * Tests Profiler::getInstance() with array parameter
     */
    public function testGetInstanceWithArrayParameter() 
    {

        Profiler::destroy();
        $profiler = Profiler::getInstance(array("timeFloating" => 3));
        $this->assertInstanceOf("Profiler\\Profiler", $profiler);
        
        $testTimeFloating = $profiler->getTimeFloating();
        $this->assertEquals(3, $testTimeFloating);
        
    }
    
    /**
     * Tests Profiler::getInstance() with array parameter containing invalid 
     * option key throws exception
     * 
     * @expectedException Profiler\Exception
     */
    public function testGetInstanceWithArrayParameterWithInvalidKeyThrowsException() 
    {

        Profiler::destroy();
        $profiler = Profiler::getInstance(array("unknownKey" => "value"));
        
    }
    
    /**
     * Tests Profiler::getMemoryUsage()
     */
    public function testGetMemoryUsage() 
    {
        
        $test = Profiler::getMemoryUsage();
        $this->assertGreaterThan(0, $test);
        
    }
    
    /**
     * Tests Profiler::getMicrotime()
     */
    public function testGetMicrotime() 
    {
        
        $test = Profiler::getMicrotime();
        $this->assertGreaterThan(0, $test);
        
    }
    
    /**
     * Tests Profiler->setRealMemoryUsage()
     */
    public function testSetGetRealMemoryUsage() 
    {

        $this->profiler->setRealMemoryUsage(true);
        $test = $this->profiler->getRealMemoryUsage();
        $this->assertTrue($test);

        $this->profiler->setRealMemoryUsage(false);
        $test = $this->profiler->getRealMemoryUsage();
        $this->assertFalse($test);
        
    }
    
    /**
     * Tests Profiler->setFloating()
     */
    public function testSetFloating() 
    {
        
        $this->profiler->setFloating(1);
        $testTime = $this->profiler->getTimeFloating();
        $testMem  = $this->profiler->getMemoryFloating();

        $this->assertEquals(1, $testTime);
        $this->assertEquals(1, $testMem);
        
    }
    
    /**
     * Tests Profiler->setTimeFloating()
     */
    public function testSetGetTimeFloating() 
    {
        
        $this->profiler->setTimeFloating(1);
        $testTime = $this->profiler->getTimeFloating();
        $this->assertEquals(1, $testTime);
        
    }
    
    /**
     * Tests Profiler->setMemoryFloating() and Profiler->getMemoryFloating()
     */
    public function testSetGetMemoryFloating() 
    {
        
        $this->profiler->setMemoryFloating(1);
        $testMem  = $this->profiler->getMemoryFloating();
        $this->assertEquals(1, $testMem);
        
    }
    
    /**
     * Tests Profiler->setDivisorSign() and Profiler->getDivisorSign()
     */
    public function testSetGetDivisorSign() 
    {
        
        $this->profiler->setDivisorSign(Profiler::BYTE);
        $test = $this->profiler->getDivisorSign();
        $this->assertEquals('byte', $test);
        
        $this->profiler->setDivisorSign(Profiler::KILOBYTE);
        $test = $this->profiler->getDivisorSign();
        $this->assertEquals('kB', $test);
        
        $this->profiler->setDivisorSign(Profiler::MEGABYTE);
        $test = $this->profiler->getDivisorSign();
        $this->assertEquals('MB', $test);
        
        $this->profiler->setDivisorSign(Profiler::GIGABYTE);
        $test = $this->profiler->getDivisorSign();
        $this->assertEquals('GB', $test);
        
    }
    
    /**
     * Tests Profiler->setDivisorSign() throws exception
     *
     * @expectedException Profiler\Exception
     */
    public function testSetDivisorSignThrowsException() 
    {

        $this->profiler->setDivisorSign('unknown unit');
        
    }
    
    /**
     * Tests Profiler->getDivisor()
     */
    public function testGetDivisor() 
    {
        
        $this->profiler->setDivisorSign(Profiler::BYTE);
        $test = $this->profiler->getDivisor();
        $this->assertEquals(1, $test);
        
        $this->profiler->setDivisorSign(Profiler::KILOBYTE);
        $test = $this->profiler->getDivisor();
        $this->assertEquals(1024, $test);
        
        $this->profiler->setDivisorSign(Profiler::MEGABYTE);
        $test = $this->profiler->getDivisor();
        $this->assertEquals(1024*1024, $test);
        
        $this->profiler->setDivisorSign(Profiler::GIGABYTE);
        $test = $this->profiler->getDivisor();
        $this->assertEquals(1024*1024*1024, $test);
        
    }
    
    /**
     * Tests Profiler->setActive(), Profiler->getActive() and Profiler->isActive()
     */
    public function testSetGetIsActive() 
    {
        
        $this->profiler->setActive(true);
        $testGet = $this->profiler->getActive();
        $testIs  = $this->profiler->isActive();
        $this->assertTrue($testGet);
        $this->assertTrue($testIs);
        
        $this->profiler->setActive(false);
        $testGet = $this->profiler->getActive();
        $testIs  = $this->profiler->isActive();
        $this->assertFalse($testGet);
        $this->assertFalse($testIs);

        Profiler::destroy();
        
    }
    
    /**
     * Tests Profiler->setWriter() with no parameter throwing an error
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSetWriterWithNoParameterThrowsError() 
    {   
        
        $this->profiler->setWriter();
    }
    
    /**
     * Tests Profiler->setWriter() with invalid parameter throwing an exception
     *
     * @expectedException Profiler\Exception
     */
    public function testSetWriterWithInvalidParameterThrowsException() 
    {   
        
        $this->profiler->setWriter('UnknownWriter');
        
    }
    
    /**
     * Tests Profiler->setWriter() with name(string) as parameter
     */
    public function testSetWriterWithNamedParameter() 
    {   
        
        $testProfiler = $this->profiler->setWriter('HtmlTable');

        $this->assertInstanceOf("Profiler\\Profiler", $testProfiler);
        $this->assertAttributeInstanceOf("Profiler\\Writer\\WriterInterface", "writer", $testProfiler);
        $this->assertAttributeInstanceOf("Profiler\\Writer\\HtmlTable", "writer", $testProfiler);
        
        $testProfiler = $this->profiler->setWriter('CsvTable');

        $this->assertInstanceOf("Profiler\\Profiler", $testProfiler);
        $this->assertAttributeInstanceOf("Profiler\\Writer\\WriterInterface", "writer", $testProfiler);
        $this->assertAttributeInstanceOf("Profiler\\Writer\\CsvTable", "writer", $testProfiler);
        
    }
    
    /**
     * Tests Profiler->setWriter() with object(WriterInterface) as parameter
     */
    public function testSetWriterWithObjectParameter() 
    {   

        $writer = new HtmlTable($this->profiler);
        $testProfiler = $this->profiler->setWriter($writer);
        
        $this->assertInstanceOf("Profiler\\Profiler", $testProfiler);
        $this->assertAttributeInstanceOf("Profiler\\Writer\\WriterInterface", "writer", $testProfiler);
        $this->assertAttributeInstanceOf("Profiler\\Writer\\HtmlTable", "writer", $testProfiler);

        $writer = new CsvTable($this->profiler);
        $testProfiler = $this->profiler->setWriter($writer);
        
        $this->assertInstanceOf("Profiler\\Profiler", $testProfiler);
        $this->assertAttributeInstanceOf("Profiler\\Writer\\WriterInterface", "writer", $testProfiler);
        $this->assertAttributeInstanceOf("Profiler\\Writer\\CsvTable", "writer", $testProfiler);
        
    }
    
    /**
     * Tests Profiler->getCheckpoints() and Profiler->clearCheckpoints()
     */
    public function testGetClearCheckpoints() 
    {
        
        Profiler::destroy();
        
        $this->profiler = Profiler::getInstance();
        
        $checkpoints = $this->profiler->getCheckpoints();
        $this->assertEmpty($checkpoints);
        
        $checkpoint = $this->profiler->start('my test-checkpoint');
        $checkpoints = $this->profiler->getCheckpoints();
        $this->assertNotEmpty($checkpoints);
        $this->assertCount(2, $checkpoints);

        $this->profiler->clearCheckpoints();
        $checkpoints = $this->profiler->getCheckpoints();
        $this->assertEmpty($checkpoints);
        
        
    }
    
    /**
     * Tests Profiler->write() throws exception when checkpoints are still 
     * active
     *
     * @expectedException Profiler\Exception
     */
    public function testWriteThrowsExceptionWhenProfilerItselfIsInactive() 
    {
        
        // create a checkpoint to display output for...
        $checkpoint = $this->profiler->start('my test-checkpoint');
        $j = 0; for ($i = 0; $i< 1000; $i++) { $j += $i; 
        }

        $this->profiler->setActive(false);
        
        $testOutput = $this->profiler->write();
        
        $this->assertContains('my test-checkpoint', $testOutput);
        
        $this->profiler->setActive(true);
        
    }
    
    /**
     * Tests Profiler->write() throws exception when there are checkpoints 
     * which are still active
     *
     * @expectedException Profiler\Exception
     */
    public function testWriteThrowsExceptionWhenCheckpointIsStillActive() 
    {
        
        Profiler::destroy();
        
        $this->profiler = Profiler::getInstance();

        $this->profiler->setWriter('CsvTable');
        
        $checkpoint = $this->profiler->start('my test-checkpoint');
        // do something, aka consume some memory for some time...
        $j = 0; for ($i = 0; $i< 1000; $i++) { $j += $i; 
        } 

        $testOutput = $this->profiler->write();
        
    }
    
    /**
     * Tests Profiler->write() throws exception if no valid writer was set
     *
     * @expectedException Profiler\Exception
     */
    public function testWriteThrowsExceptionIfNoValidWriterWasSet() 
    {
        
        Profiler::destroy();
        
        $this->profiler = Profiler::getInstance();
    
        $checkpoint = $this->profiler->start('my test-checkpoint');
        // do something, aka consume some memory for some time...
        $j = 0; for ($i = 0; $i< 1000; $i++) { $j += $i; 
        }
        $checkpoint->stop();
        
        $testOutput = $this->profiler->write();
        
    }
    
    /**
     * Tests Profiler->write()
     */
    public function testWrite() 
    {
        
        Profiler::destroy();
        
        $this->profiler = Profiler::getInstance();
        $this->profiler->setWriter('CsvTable');
        
        $checkpoint = $this->profiler->start('my test-checkpoint');
        $j = 0; for ($i = 0; $i< 1000; $i++) { $j += $i; 
        } // do something, aka consume some memory for some time...
        $checkpoint->stop();
        
        $testOutput = $this->profiler->write();
        
        $this->assertContains('my test-checkpoint', $testOutput);
        
    }
    
    /**
     * Tests Profiler->start()
     */
    public function testStart() 
    {
        
        Profiler::destroy();
        
        $this->profiler = Profiler::getInstance();
        
        $checkpoint = $this->profiler->start('my test-checkpoint');
        
        $checkpoints = $this->profiler->getCheckpoints();
        

        $this->assertInstanceOf("Profiler\\Checkpoint\\CheckpointInterface", $checkpoint);
        $this->assertInstanceOf("Profiler\\Checkpoint\\CheckpointAbstract", $checkpoint);

        $this->assertInstanceOf("Profiler\\Checkpoint\\CheckpointInterface", $checkpoints[0]);
        $this->assertInstanceOf("Profiler\\Checkpoint\\CheckpointAbstract", $checkpoints[0]);
        //$this->assertEquals($checkpoint, $checkpoints[0]);
        
    }
    
    /**
     * Tests Profiler->start()
     */
    public function testStartCreatesDummyCheckpointIfProfilerIsInactive() 
    {
        
        Profiler::destroy();
        
        $this->profiler = Profiler::getInstance();
        $this->profiler->setActive(false);

        $this->assertFalse($this->profiler->isActive());
        
        $checkpoint = $this->profiler->start('test dummy');
        
        $this->assertInstanceOf("Profiler\\Checkpoint\\Dummy", $checkpoint);
    }
    
    /**
     * Tests Profiler->stop() with no parameter throwing an error
     *
     * @expectedException TypeError
     */
    public function testStopWithNoParameterThrowsError() 
    {
        
        $this->profiler->stop();
    }
    
    /**
     * Tests Profiler->stop() with invalid parameter throwing an error
     *
     * @expectedException TypeError
     */
    public function testStopWithInvalidParameterThrowsError() 
    {
        
        $this->profiler->stop('stop it!');
    }
    
    /**
     * Tests Profiler->stop() 
     */
    public function testStop() 
    {
        
        Profiler::destroy();
        
        $this->profiler = Profiler::getInstance();
        $checkpoint = $this->profiler->start('my checkpoint');

        $checkpoints = $this->profiler->getCheckpoints();
        $this->assertInstanceOf("Profiler\\Checkpoint\\CheckpointInterface", $checkpoints[0]);
        $this->assertInstanceOf("Profiler\\Checkpoint\\CheckpointAbstract", $checkpoints[0]);
        $this->assertTrue($this->profiler->isActive());
        
        $this->profiler->stop($checkpoints[0]);
        
        $this->assertFalse($checkpoints[0]->isActive());
        
    }
}

