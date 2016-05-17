<?php

/**
 * profiler writer abstrct
 *
 * @category       php
 * @package        Profiler
 * @author         Björn Bartels <coding@bjoernbartels.earth>
 * @link           https://gitlab.bjoernbartels.earth/groups/php
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright      copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */

/**
 * @see Profiler_Writer_Interface
 */
require_once 'Profiler/Writer/Interface.php';

abstract class Profiler_Writer_Abstract
    implements Profiler_Writer_Interface
{
    /**
     * @access protected
     * @var    Profiler
     */
    protected $_profiler = null;
    
    /**
     * @access public
     * @param  Profiler $profiler
     * @return void
     */
    public function __construct(Profiler $profiler)
    {
        $this->_profiler = $profiler;
    }
    
    /**
     * @access protected
     * @return Profiler
     */
    protected function _getProfiler()
    {
        return $this->_profiler;
    }
}