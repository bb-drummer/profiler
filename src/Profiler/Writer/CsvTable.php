<?php
/**
 * profiler CSV table writer
 */
namespace Profiler\Writer;

use Profiler\Profiler;
use Profiler\Checkpoint;
use Profiler\Writer\WriterAbstract;

/**
 * profiler CSV table writer
 * 
 * profiling data writer creating a comma-separated-value (CSV) list
 *
 * @category  php
 * @package   Profiler
 * @author    Björn Bartels <coding@bjoernbartels.earth>
 * @link      https://gitlab.bjoernbartels.earth/groups/php
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */
class CsvTable
    extends WriterAbstract
{
    /**
     * get complete profiler output
     * 
     * @access public
     * @return string
     */
    public function get()
    {
        $table = "Idx,Name,TimeStart,TimeStop,TimeDiff,MemoryStart,MemoryStop,MemoryDiff" . PHP_EOL;
        
        $checkpoints = $this->getProfiler()->getCheckpoints();
        $application = null;
        $indexLength = strlen(count($checkpoints));
        
        foreach ($checkpoints as $index => $checkpoint)
        {
            if ($index == 0) {
                $application = $checkpoint;
            }
            
            $table .= $this->getRow(
                str_pad($index, $indexLength, '0', STR_PAD_LEFT),
                $checkpoint,
                $application
            );
        }
        
        $table .= "" . PHP_EOL;
        
        return $table;
    }
    
    /**
     * retrieve single row/checkpoint output
     * 
     * @access protected
     * @param  integer    $index
     * @param  Checkpoint $checkpoint
     * @param  Checkpoint $application
     * @return string
     */
    protected function getRow($index, Checkpoint $checkpoint, Checkpoint $application) 
    {
    
        $profiler       = $this->getProfiler();
        $divisor        = $profiler->getDivisor();
        $divisorSign    = $profiler->getDivisorSign();
        
        $appStartTime   = $application->startTime;
        
        $timeFloating   = $profiler->getTimeFloating();
        $rawStartTime   = $checkpoint->startTime;
        $rawStopTime    = $checkpoint->stopTime;
        $startTime      = number_format($rawStartTime - $appStartTime, $timeFloating, ',', '');
        $stopTime       = number_format($rawStopTime - $appStartTime,  $timeFloating, ',', '');
        $diffTime       = number_format($rawStopTime - $rawStartTime, $timeFloating, ',', '');
        
        $memoryFloating = $profiler->getMemoryFloating();
        $rawStartMemory = $checkpoint->startMemory;
        $rawStopMemory  = $checkpoint->stopMemory;
        $startMemory    = number_format($rawStartMemory / $divisor, $memoryFloating, ',', '') . ' ' . $divisorSign;
        $stopMemory     = number_format($rawStopMemory / $divisor, $memoryFloating, ',', '') . ' ' . $divisorSign;
        $diffMemory     = number_format(($rawStopMemory - $rawStartMemory) / $divisor, $memoryFloating, ',', '') . ' ' . $divisorSign;
        
        $indention      = str_repeat('>', $checkpoint->depth * 1);
        
        $row = '"'.$index.'","'.$indention.''.$checkpoint->title.'","'.$startTime.'s","'.$stopTime.'s","'.$diffTime.'s","'.$startMemory.'","'.$stopMemory.'","'.$diffMemory.'"' . PHP_EOL;
        
        return $row;
    }
    
}