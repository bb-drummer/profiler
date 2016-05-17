<?php

/**
 * profiler checkpoint interface
 *
 * @category       php
 * @package        Profiler
 * @author         Björn Bartels <coding@bjoernbartels.earth>
 * @link           https://gitlab.bjoernbartels.earth/groups/php
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright      copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */
interface Profiler_Checkpoint_Interface
{
    /**
     * @access public
     * @param  string  $title
     * @param  integer $depth
     * @return void
     */
    public function __construct($title, $depth = null);
    
    /**
     * @access public
     * @param  boolean $manual
     * @return Profiler_Checkpoint
     */
    public function stop($manual = true);
    
    /**
     * @access public
     * @return array
     */
    public function getInfo();
    
    /**
     * @access public
     * @return boolean
     */
    public function isActive();
}