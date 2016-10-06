<?php
/**
 * profiler checkpoint abstract
 */
namespace Profiler\Checkpoint;

use Profiler\Checkpoint\CheckpointInterface;
use Profiler\Checkpoint\CheckpointException;

/**
 * profiler checkpoint abstract
 * 
 * @category  php
 * @package   Profiler
 * @author    Björn Bartels <coding@bjoernbartels.earth>
 * @link      https://gitlab.bjoernbartels.earth/groups/php
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 *
 * @property float $startTime
 * @property float $stopTime
 * @property integer $startMemory
 * @property integer $stopMemory
 */
abstract class CheckpointAbstract implements CheckpointInterface
{
    /**
     * full info array
     * 
     * @access protected
     * @var    array
     */
    protected $info = array(
        'title'       => null,
        'startTime'   => null,
        'startMemory' => null,
        'stopTime'    => null,
        'stopMemory'  => null,
        'depth'       => null
    );
    
    /**
     * is checkpoint active?
     * 
     * @access protected
     * @var    boolean
     */
    protected $active = true;
    
    /**
     * get full info array
     * 
     * @access public
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }
    
    /**
     * check if checkpoint is active
     * 
     * @access public
     * @return boolean
     */
    public function isActive()
    {
        return (bool)$this->active;
    }
    
    /**
     * get single info property
     * 
     * @access public
     * @param  string $name
     * @throws CheckpointException
     * @return mixed
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->info)) {
            throw new CheckpointException(
                sprintf(
                    'Could not get information by name "%s".', $name
                )
            );
        }
        return $this->info[$name];
    }
}
