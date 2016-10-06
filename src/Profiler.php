<?php
/**
 * profiler main object
 */
namespace Profiler;

use Profiler\Exception;
use Profiler\Checkpoint;
use Profiler\Checkpoint\CheckpointInterface;
use Profiler\Checkpoint\CheckpointAbstract;
use Profiler\Checkpoint\Dummy;
use Profiler\Writer\WriterInterface;

/**
 * profiler main object
 *
 * @category  php
 * @package   Profiler
 * @author    Björn Bartels <coding@bjoernbartels.earth>
 * @link      https://gitlab.bjoernbartels.earth/groups/php
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */
class Profiler
{
    /**
     * Show memory usage in byte
     */
    const BYTE = 'byte';
    
    /**
     * Show memory usage in kilobyte
     */
    const KILOBYTE = 'kB';
    
    /**
     * Show memory usage in megabyte
     */
    const MEGABYTE = 'MB';
    
    /**
     * Show memory usage in gigabyte
     */
    const GIGABYTE = 'GB';
    
    /**
     * initial profiling starting checkpoint
     * 
     * @access protected
     * @var    CheckpointAbstract
     */
    protected $application = null;
    
    /**
     * internal dummy checkpoint
     * 
     * @access protected
     * @var    CheckpointAbstract
     */
    protected $dummy = null;
    
    /**
     * list of checkpoints to profile
     *
     * @access protected
     * @var    array
     */
    protected $checkpoints = array();
    
    /**
     * time floating-point number precision
     * 
     * @access protected
     * @var    integer
     */
    protected $timeFloating = 6;
    
    /**
     * memory floating-point number precision
     * 
     * @access protected
     * @var    integer
     */
    protected $memoryFloating = 6;
    
    /**
     * internal depth count
     * 
     * @access protected
     * @var    integer
     */
    protected $depth = 0;
    
    /**
     * (internal) memory unit sign divisor
     * 
     * @access protected
     * @var    integer
     */
    protected $divisor = 1024;
    
    /**
     * memory unit sign
     * 
     * @access protected
     * @var    string
     */
    protected $divisorSign = self::MEGABYTE;
    
    /**
     * get php's 'real' memory usage
     * 
     * @access protected
     * @var    boolean
     */
    protected $useRealMemoryUsage = false;

    /**
     * is this profiler active?
     *
     * @access protected
     * @var    boolean
     */
    protected $active = true;
    
    /**
     * writer instance
     * 
     * @access protected
     * @var    WriterInterface
     */
    protected $writer = null;
    
    /**
     * Profiler singleton object instance
     * 
     * @static
     * @access protected
     * @var    Profiler
     */
    protected static $instance = null;
    
    /**
     * create/get profiler instance
     *  
     * @static
     * @access public
     * @param  array|Zend_Config $options
     * @return Profiler
     */
    public static function getInstance($options = array())
    {
        if (self::$instance === null) {
            self::$instance = new self($options);
        }
        return self::$instance;
    }
    
    /**
     * class constructor
     * 
     * @access protected
     * @param  array $options
     * @throws Profiler\Exception
     */
    protected function __construct($options = array())
    {
        
        if (!is_array($options)) {
            throw new Exception('Invalid options format given.');
        }
        
        foreach ($options as $name => $value)
        {
            $methodName = 'set' . ucfirst($name);
            if (!method_exists($this, $methodName)) {
                throw new Exception(
                    sprintf(
                        'Invalid or unknown option "%s".', $name
                    )
                );
            }
            $this->$methodName($value);
        }
        
        if (!isset($options['active'])) {
            $this->setActive(true);
        }
    }
    
    /**
     * retrieve memory usage from system/php
     * 
     * @access public
     * @return integer
     * @see    http://php.net/manual/de/function.memory-get-usage.php
     */
    public static function getMemoryUsage()
    {
        return memory_get_usage(
            Profiler::getInstance()->getRealMemoryUsage()
        );
    }
    
    /**
     * return current Unix timestamp with microseconds
     * 
     * @access public
     * @return double
     */
    public static function getMicrotime()
    {
        return microtime(true);
    }
    
    /**
     * set to use php's 'real' memory usage
     * 
     * @access public
     * @param  boolean $use
     * @return Profiler
     * @see    http://php.net/manual/de/function.memory-get-usage.php
     */
    public function setRealMemoryUsage($use = true)
    {
        $this->useRealMemoryUsage = (bool)$use;
        return $this;
    }
    
    /**
     * use php's 'real' memory usage?
     * 
     * @access public
     * @return boolean
     * @see    http://php.net/manual/de/function.memory-get-usage.php
     */
    public function getRealMemoryUsage()
    {
        return $this->useRealMemoryUsage;
    }
    
    /**
     * set time and memory floating-point numbers precision
     * 
     * @access public
     * @param  integer $floating
     * @return Profiler
     */
    public function setFloating($floating = 6)
    {
        $this->timeFloating = $floating;
        $this->memoryFloating = $floating;
        return $this;
    }
    
    /**
     * set time floating-point numbers precision
     * 
     * @access public
     * @param  integer $floating
     * @return Profiler
     */
    public function setTimeFloating($floating = 6)
    {
        $this->timeFloating = $floating;
        return $this;
    }
    
    /**
     * get time floating-point numbers precision
     * 
     * @access public
     * @return integer
     */
    public function getTimeFloating()
    {
        return $this->timeFloating;
    }
    
    /**
     * set memory floating-point numbers precision
     * 
     * @access public
     * @param  integer $floating
     * @return Profiler
     */
    public function setMemoryFloating($floating = 6)
    {
        $this->memoryFloating = $floating;
        return $this;
    }
    
    /**
     * get memory floating-point numbers precision
     * 
     * @access public
     * @return integer
     */
    public function getMemoryFloating()
    {
        return $this->memoryFloating;
    }
    
    /**
     * set memory unit sign and internal divisor
     * 
     * @access public
     * @param  string $divisorSign
     * @throws Profiler\Exception
     * @return Profiler
     */
    public function setDivisorSign($divisorSign = self::MEGABYTE)
    {
        if ($divisorSign == self::BYTE) {
            $this->divisor = 1;
        } elseif ($divisorSign == self::KILOBYTE) {
            $this->divisor = 1024;
        } elseif ($divisorSign == self::MEGABYTE) {
            $this->divisor = 1048576;
        } elseif ($divisorSign == self::GIGABYTE) {
            $this->divisor = 1073741824;
        } else
        {
            throw new Exception(
                sprintf(
                    'Unknown divisor sign "%s".', $divisorSign
                )
            );
        }
        
        $this->divisorSign = $divisorSign;
        return $this;
    }
    
    /**
     * get internal divisor
     * 
     * @access public
     * @return integer
     */
    public function getDivisor()
    {
        return $this->divisor;
    }
    
    /**
     * get memory unit sign
     * 
     * @access public
     * @return string
     */
    public function getDivisorSign()
    {
        return $this->divisorSign;
    }
    
    /**
     * set if profiler is active
     * 
     * @access public
     * @param  boolean $active
     * @return Profiler
     */
    public function setActive($active = true)
    {
        $this->active = (bool)$active;
        return $this;
    }
    
    /**
     * get if profiler is active
     * 
     * @access public
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
    
    /**
     * is profiler active?
     * 
     * @access public
     * @uses   Profiler::getActive()
     * @return boolean
     */
    public function isActive()
    {
        return $this->getActive();
    }
    
    /**
     * set current profiling writer instance
     * 
     * @access public
     * @param  string|WriterInterface $writer
     * @return Profiler
     */
    public function setWriter($writer)
    {
        if (is_string($writer) && file_exists(__DIR__ . '/Profiler/Writer/' . $writer . '.php')) {
            $className = 'Profiler\\Writer\\' . $writer;
            $writer = new $className($this);
        }
        
        if (!$writer instanceof WriterInterface) {
            throw new Exception(
                'Given writer must be an instance of ' .
                'Profiler\\Writer\\WriterInterface.'
            );
        }
        
        $this->writer = $writer;
        return $this;
    }
    
    /**
     * start profiling new checkpoint, increase internal depth count
     * 
     * @access public
     * @param  string $title
     * @throws Profiler\Exception
     * @return CheckpointAbstract
     */
    public function start($title)
    {
        if (!$this->isActive()) {
            if ($this->dummy === null) {
                $this->dummy = new Dummy('');
            }
            return $this->dummy;
        }
        
        if (!count($this->checkpoints)) {
            $this->attach(
                $this->application = new Checkpoint(
                    'Application', $this->depth = 0
                )
            );
        }
        
        $this->attach(
            $checkpoint = new Checkpoint(
                $title, ++$this->depth
            )
        );
        
        return $checkpoint;
    }
    
    /**
     * stop profiling a given checkpoint
     * 
     * @access public
     * @param  CheckpointAbstract $checkpoint
     * @return Profiler
     */
    public function stop(CheckpointAbstract $checkpoint)
    {
        if ($this->isActive()) {
            $this->detach($checkpoint);
        }
        return $this;
    }
    
    /**
     * retrieve list of curretn checkpoints
     * 
     * @access public
     * @return array
     */
    public function getCheckpoints()
    {
        return $this->checkpoints;
    }
    
    /**
     * empty the list of curretn checkpoints
     * 
     * @access public
     * @return Profiler
     */
    public function clearCheckpoints()
    {
        $this->checkpoints = [];
        return $this;
    }
    
    /**
     * invoke writer and create output
     * 
     * @access public
     * @throws Exception
     * @return mixed
     */
    public function write()
    {
        if (!$this->isActive()) {
            throw new Exception(
                sprintf(
                    'Cannot write profiling because profiler is not active.'
                )
            );
        }
        
        if (!$this->writer instanceof WriterInterface) {
            throw new Exception(
                sprintf(
                    'Cannot write profiling because no valid writer was set.'
                )
            );
        }
        
        $this->application->stop();
        foreach ($this->checkpoints as $checkpoint)
        {
            if (!$checkpoint->isActive()) {
                continue;
            }
            
            throw new Exception(
                sprintf(
                    'Found active checkpoint: "%s".', $checkpoint->title
                )
            );
        }
        
        return $this->writer->get();
    }
    
    /**
     * add new checkpoint to checkpoint-list
     * 
     * @access protected
     * @param  CheckpointInterface $checkpoint
     * @return Profiler
     */
    protected function attach(CheckpointInterface $checkpoint)
    {
        $this->checkpoints[] = $checkpoint;
        return $this;
    }
    
    /**
     * stops checkpoint, decrease internal depth count
     * 
     * @access protected
     * @param  CheckpointAbstract $checkpoint
     * @return Profiler
     */
    protected function detach(CheckpointAbstract $checkpoint)
    {
        --$this->depth;
        $checkpoint->stop(false);
        return $this;
    }
    
    /**
     * destroys current profiler instance
     * 
     * @access public
     */
    public static function destroy() 
    {
        self::$instance = null;
    }
    
}
