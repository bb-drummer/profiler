<?php
/**
 * profiler checkpoint interface
 */
namespace Profiler\Checkpoint;

/**
 * profiler checkpoint interface
 *
 * @category  php
 * @package   Profiler
 * @author    Björn Bartels <coding@bjoernbartels.earth>
 * @link      https://gitlab.bjoernbartels.earth/groups/php
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */
interface CheckpointInterface
{
    /**
     * class constructor
     * 
     * @access public
     * @param  string  $title
     * @param  integer $depth
     * @return void
     */
    public function __construct($title, $depth = null);
    
    /**
     * stop profiling the checkpoint
     * 
     * @access public
     * @param  boolean $manual
     * @return Profiler\Checkpoint
     */
    public function stop($manual = true);
    
    /**
     * retrieve checkpoint meta info-set
     * 
     * @access public
     * @return array
     */
    public function getInfo();
    
    /**
     * is checkpoint active?
     * 
     * @access public
     * @return boolean
     */
    public function isActive();
}
