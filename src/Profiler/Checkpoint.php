<?php
/**
 * profiler checkpoint object
 */
namespace Profiler;

use Profiler\Profiler;
use Profiler\Checkpoint;
use Profiler\Checkpoint\CheckpointAbstract;

/**
 * profiler checkpoint object
 *
 * @property float $startTime
 * @property float $stopTime
 * @property integer $startMemory
 * @property integer $stopMemory
 * 
 * @category  php
 * @package   Profiler
 * @author    Björn Bartels <coding@bjoernbartels.earth>
 * @link      https://gitlab.bjoernbartels.earth/groups/php
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */
class Checkpoint extends CheckpointAbstract
{
    
    /**
     * class constructor
     * 
     * @access public
     * @param  string  $title
     * @param  integer $depth
     */
    public function __construct($title, $depth = null)
    {
        $this->info['title']       = $title;
        $this->info['startTime']   = Profiler::getMicrotime();
        $this->info['startMemory'] = Profiler::getMemoryUsage();
        $this->info['depth']       = $depth;
    }
    
    /**
     * stop active checkpoint
     * 
     * @access public
     * @param  boolean $manual
     * @return Profiler\Checkpoint
     */
    public function stop($manual = true)
    {
        if (!$this->isActive()) {
            return null;
        }
        
        $this->info['stopTime']   = Profiler::getMicrotime();
        $this->info['stopMemory'] = Profiler::getMemoryUsage();
        $this->active = false;
        
        if ($manual === true) {
            Profiler::getInstance()->stop($this);
        }
    }
}
