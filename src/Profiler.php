<?php

/**
 * profiler main object
 *
 * @category       php
 * @package        Profiler
 * @author         Björn Bartels <coding@bjoernbartels.earth>
 * @link           https://gitlab.bjoernbartels.earth/groups/php
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright      copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */

/**
 * @see Profiler_Exception
 */
require_once 'Profiler/Exception.php';

/**
 * @see Profiler_Checkpoint
 */
require_once 'Profiler/Checkpoint.php';


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
     * @access protected
     * @var    Profiler_Checkpoint_Abstract
     */
    protected $_application = null;
    
    /**
     * @access protected
     * @var    Profiler_Checkpoint_Abstract
     */
    protected $_dummy = null;
    
    /**
     * @access protected
     * @var    array
     */
    protected $_checkpoints = array();
    
    /**
     * @access protected
     * @var    integer
     */
    protected $_timeFloating = 6;
    
    /**
     * @access protected
     * @var    integer
     */
    protected $_memoryFloating = 6;
    
    /**
     * @access protected
     * @var    integer
     */
    protected $_depth = 0;
    
    /**
     * @access protected
     * @var    integer
     */
    protected $_divisor = 1024;
    
    /**
     * @access protected
     * @var    string
     */
    protected $_divisorSign = self::MEGABYTE;
    
    /**
     * @access protected
     * @var    boolean
     */
    protected $_useRealMemoryUsage = true;
    
    /**
     * @access protected
     * @var    Profiler_Writer_Interface
     */
    protected $_writer = null;
    
    /**
     * @static
     * @access protected
     * @var    Profiler
     */
    protected static $_instance = null;
    
    /**
     * @static
     * @access public
     * @param  array|Zend_Config $options
     * @return Profiler
     */
    public static function getInstance($options = array())
    {
        if (self::$_instance === null) {
            self::$_instance = new self($options);
        }
        return self::$_instance;
    }
    
    /**
     * @access protected
     * @param  array $options
     * @throws Profiler_Exception
     * @return void
     */
    protected function __construct($options = array())
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        
        if (!is_array($options)) {
            throw new Profiler_Exception('Invalid options format given.');
        }
        
        foreach ($options as $name => $value)
        {
            $methodName = 'set' . ucfirst($name);
            if (!method_exists($this, $methodName)) {
                throw new Profiler_Exception(sprintf(
                    'Invalid or unknown option "%s".', $name
                ));
            }
            $this->$methodName($value);
        }
        
        if (!isset($options['active'])) {
            $this->setActive(true);
        }
    }
    
    /**
     * @access public
     * @return integer
     */
    public static function getMemoryUsage()
    {
        return memory_get_usage(
            Profiler::getInstance()->getRealMemoryUsage()
        );
    }
    
    /**
     * @access public
     * @return integer
     */
    public static function getMicrotime()
    {
        return microtime(true);
    }
    
    /**
     * @access public
     * @param  boolean $use
     * @return Profiler
     */
    public function setRealMemoryUsage($use = true)
    {
        $this->_useRealMemoryUsage = (bool)$use;
        return $this;
    }
    
    /**
     * @access public
     * @return boolean
     */
    public function getRealMemoryUsage()
    {
        return $this->_useRealMemoryUsage;
    }
    
    /**
     * @access public
     * @param  integer $floating
     * @return Profiler
     */
    public function setFloating($floating = 6)
    {
        $this->_timeFloating = $floating;
        $this->_memoryFloating = $floating;
        return $this;
    }
    
    /**
     * @access public
     * @param  integer $floating
     * @return Profiler
     */
    public function setTimeFloating($floating = 6)
    {
        $this->_timeFloating = $floating;
        return $this;
    }
    
    /**
     * @access public
     * @return integer
     */
    public function getTimeFloating()
    {
        return $this->_timeFloating;
    }
    
    /**
     * @access public
     * @param  integer $floating
     * @return Profiler
     */
    public function setMemoryFloating($floating = 6)
    {
        $this->_memoryFloating = $floating;
        return $this;
    }
    
    /**
     * @access public
     * @return integer
     */
    public function getMemoryFloating()
    {
        return $this->_memoryFloating;
    }
    
    /**
     * @access public
     * @param  string $divisorSign
     * @throws Profiler_Exception
     * @return Profiler
     */
    public function setDivisorSign($divisorSign = self::MEGABYTE)
    {
        if ($divisorSign == self::BYTE) {
            $this->_divisor = 1;
        } elseif ($divisorSign == self::KILOBYTE) {
            $this->_divisor = 1024;
        } elseif ($divisorSign == self::MEGABYTE) {
            $this->_divisor = 1048576;
        } elseif ($divisorSign == self::GIGABYTE) {
            $this->_divisor = 1073741824;
        }
        else
        {
            throw new Profiler_Exception(sprintf(
                'Unknown divisor sign "%s".', $divisorSign
            ));
        }
        
        $this->_divisorSign = $divisorSign;
        return $this;
    }
    
    /**
     * @access public
     * @return integer
     */
    public function getDivisor()
    {
        return $this->_divisor;
    }
    
    /**
     * @access public
     * @return string
     */
    public function getDivisorSign()
    {
        return $this->_divisorSign;
    }
    
    /**
     * @access public
     * @param  boolean $active
     * @return integer
     */
    public function setActive($active = true)
    {
        $this->_active = (bool)$active;
        return $this;
    }
    
    /**
     * @access public
     * @uses   Profiler::getActive()
     * @return boolean
     */
    public function getActive()
    {
        return $this->_active;
    }
    
    /**
     * @access public
     * @uses   Profiler::getActive()
     * @return boolean
     */
    public function isActive()
    {
        return $this->getActive();
    }
    
    /**
     * @access public
     * @param  string|Profiler_Writer_Interface $writer
     * @return Profiler
     */
    public function setWriter($writer)
    {
        if (is_string($writer)) {
            $className = 'Profiler_Writer_' . $writer;
            Zend_Loader::loadClass($className);
            $writer = new $className($this);
        }
        
        if (!$writer instanceof Profiler_Writer_Interface) {
            throw new Profiler_Exception(
                'Given writer must be an instance of ' .
                'Profiler_Writer_Interface.'
            );
        }
        
        $this->_writer = $writer;
        return $this;
    }
    
    /**
     * @access public
     * @param  string $title
     * @throws Profiler_Exception
     * @return Profiler_Checkpoint_Abstract
     */
    public function start($title)
    {
        if (!$this->isActive())
        {
            if ($this->_dummy === null) {
                require_once 'Profiler/Checkpoint/Dummy.php';
                $this->_dummy = new Profiler_Checkpoint_Dummy('');
            }
            return $this->_dummy;
        }
        
        if (!count($this->_checkpoints))
        {
            $this->_attach($this->_application = new Profiler_Checkpoint(
                'Application', $this->_depth = 0
            ));
        }
        
        $this->_attach($checkpoint = new Profiler_Checkpoint(
            $title, ++$this->_depth
        ));
        
        return $checkpoint;
    }
    
    /**
     * @access public
     * @param  Profiler_Checkpoint_Abstract $checkpoint
     * @return Profiler
     */
    public function stop(Profiler_Checkpoint_Abstract $checkpoint)
    {
        if ($this->isActive()) {
            $this->_detach($checkpoint);
        }
        return $this;
    }
    
    /**
     * @access public
     * @return array
     */
    public function getCheckpoints()
    {
        return $this->_checkpoints;
    }
    
    /**
     * @access public
     * @throws Profiler_Exception
     * @return mixed
     */
    public function write()
    {
        if (!$this->isActive())
        {
            throw new Profiler_Exception(sprintf(
                'Cannot write profiling because profiler is not active.'
            ));
        }
        
        if (!$this->_writer instanceof Profiler_Writer_Interface)
        {
            throw new Profiler_Exception(sprintf(
                'Cannot write profiling because no valid writer was set.'
            ));
        }
        
        $this->_application->stop();
        foreach ($this->_checkpoints as $checkpoint)
        {
            if (!$checkpoint->isActive()) {
                continue;
            }
            
            throw new Profiler_Exception(sprintf(
                'Found active checkpoint: "%s".', $checkpoint->title
            ));
        }
        
        return $this->_writer->get($this);
    }
    
    /**
     * @access protected
     * @param  Profiler_Checkpoint_Abstract $checkpoint
     * @return Profiler
     */
    protected function _attach(Profiler_Checkpoint_Interface $checkpoint)
    {
        $this->_checkpoints[] = $checkpoint;
        return $this;
    }
    
    /**
     * @access protected
     * @param  Profiler_Checkpoint_Abstract $checkpoint
     * @return Profiler
     */
    protected function _detach(Profiler_Checkpoint_Abstract $checkpoint)
    {
        --$this->_depth;
        $checkpoint->stop(false);
        return $this;
    }
}