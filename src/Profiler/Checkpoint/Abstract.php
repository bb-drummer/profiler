<?php

/**
 * profiler checkpoint abstract
 *
 * @category       php
 * @package        Profiler
 * @author         Björn Bartels <coding@bjoernbartels.earth>
 * @link           https://gitlab.bjoernbartels.earth/groups/php
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright      copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */

/**
 * @see Profiler_Checkpoint_Interface
 */
require_once 'Profiler/Checkpoint/Interface.php';

/**
 * @see Profiler_Checkpoint_Exception
 */
require_once 'Profiler/Checkpoint/Exception.php';

abstract class Profiler_Checkpoint_Abstract
    implements Profiler_Checkpoint_Interface
{
    /**
     * @access protected
     * @var    array
     */
    protected $_info = array(
        'title'       => null,
        'startTime'   => null,
        'startMemory' => null,
        'stopTime'    => null,
        'stopMemory'  => null,
        'depth'       => null
    );
    
    /**
     * @access protected
     * @var    boolean
     */
    protected $_active = true;
    
    /**
     * @access public
     * @return array
     */
    public function getInfo()
    {
        return $this->_info;
    }
    
    /**
     * @access public
     * @return boolean
     */
    public function isActive()
    {
        return (bool)$this->_active;
    }
    
    /**
     * @access public
     * @param  string $name
     * @throws Profiler_Checkpoint_Exception
     * @return mixed
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->_info)) {
            throw new Profiler_Checkpoint_Exception(sprintf(
                'Could not get information by name "%s".', $name
            ));
        }
        return $this->_info[$name];
    }
}