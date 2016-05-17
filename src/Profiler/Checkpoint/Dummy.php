<?php

/**
 * profiler checkpoint dummy
 *
 * @category       php
 * @package        Profiler
 * @author         Björn Bartels <coding@bjoernbartels.earth>
 * @link           https://gitlab.bjoernbartels.earth/groups/php
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright      copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */

/**
 * @see Profiler_Checkpoint_Abstract
 */
require_once 'Profiler/Checkpoint/Abstract.php';

class Profiler_Checkpoint_Dummy
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
        $this->_active = false;
    }
    
    /**
     * @access public
     * @param  boolean $manual
     * @return Profiler_Checkpoint
     */
    public function stop($manual = true)
    {
        /* do nothing */
    }
}