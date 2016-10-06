<?php
/**
 * profiler checkpoint dummy
 */
namespace Profiler\Checkpoint;

use Profiler\Checkpoint\CheckpointAbstract;

/**
 * profiler checkpoint dummy
 *
 * @category  php
 * @package   Profiler
 * @author    Björn Bartels <coding@bjoernbartels.earth>
 * @link      https://gitlab.bjoernbartels.earth/groups/php
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */
class Dummy extends CheckpointAbstract
{
    /**
     * class constructor
     * 
     * @access public
     * @param  string  $title
     * @param  integer $depth
     * @return void
     */
    public function __construct($title, $depth = null)
    {
        $this->active = false;
    }
    
    /**
     * stop profiling this checkpoint
     * 
     * @access public
     * @param  boolean $manual
     * @return Checkpoint
     */
    public function stop($manual = true)
    {
        /* do nothing */
    }
    
}
