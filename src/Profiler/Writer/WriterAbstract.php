<?php
/**
 * profiler writer abstrct
 */
namespace Profiler\Writer;

use Profiler\Profiler;
use Profiler\Writer\WriterInterface;

/**
 * profiler writer abstract
 * 
 * generic profiling data (output) writer abstract class
 *
 * @category  php
 * @package   Profiler
 * @author    Björn Bartels <coding@bjoernbartels.earth>
 * @link      https://gitlab.bjoernbartels.earth/groups/php
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */
abstract class WriterAbstract implements WriterInterface
{
    /**
     * related profiler instance
     * 
     * @access protected
     * @var    Profiler
     */
    protected $profiler = null;
    
    /**
     * class constructor
     * 
     * @access public
     * @param  Profiler $profiler
     */
    public function __construct(Profiler $profiler)
    {
        $this->profiler = $profiler;
    }
    
    /**
     * get related profiler instance
     * 
     * @access protected
     * @return Profiler
     */
    protected function getProfiler()
    {
        return $this->profiler;
    }
}
