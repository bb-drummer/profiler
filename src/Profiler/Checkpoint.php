<?php

/**
 * profiler checkpoint object
 *
 * @category       php
 * @package        Profiler
 * @author         Björn Bartels <coding@bjoernbartels.earth>
 * @link           https://gitlab.bjoernbartels.earth/groups/php
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright      copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */

/**
 * @see Profiler
 */
require_once 'Profiler.php';

/**
 * @see Profiler_Checkpoint_Abstract
 */
require_once 'Profiler/Checkpoint/Abstract.php';

class Profiler_Checkpoint
    extends Profiler_Checkpoint_Abstract
{
    
    /**
     * @access public
     * @param  string  $title
     * @param  integer $depth
     * @return void
     */
    public function __construct($title, $depth = null)
    {
        $this->_info['title']       = $title;
        $this->_info['startTime']   = Profiler::getMicrotime();
        $this->_info['startMemory'] = Profiler::getMemoryUsage();
        $this->_info['depth']       = $depth;
    }
    
    /**
     * @access public
     * @param  boolean $manual
     * @return Profiler_Checkpoint
     */
    public function stop($manual = true)
    {
        if (!$this->isActive()) {
            return null;
        }
        
        $this->_info['stopTime']   = Profiler::getMicrotime();
        $this->_info['stopMemory'] = Profiler::getMemoryUsage();
        $this->_active = false;
        
        if ($manual === true) {
            Profiler::getInstance()->stop($this);
        }
    }
}