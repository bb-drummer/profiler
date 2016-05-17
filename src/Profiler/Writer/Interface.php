<?php

/**
 * profiler writer interface
 *
 * @category       php
 * @package        Profiler
 * @author         Björn Bartels <coding@bjoernbartels.earth>
 * @link           https://gitlab.bjoernbartels.earth/groups/php
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright      copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */

interface Profiler_Writer_Interface
{
    /**
     * @access public
     * @return string
     */
    public function get();
    
    /**
     * @access public
     * @param  Profiler $profiler
     * @return void
     */
    public function __construct(Profiler $profiler);
}