<?php

/**
 * profiler CSV table writer
 *
 * @category       php
 * @package        Profiler
 * @author         Björn Bartels <coding@bjoernbartels.earth>
 * @link           https://gitlab.bjoernbartels.earth/groups/php
 * @license        http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright      copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */

/**
 * @see Profiler_Writer_Abstract
 */
require_once 'Profiler/Writer/Abstract.php';

class Profiler_Writer_CsvTable
    extends Profiler_Writer_Abstract
{
    /**
     * @access public
     * @return string
     */
    public function get()
    {
        $table = "Idx,Name,TimeStart,TimeStop,TimeDiff,MemoryStart,MemoryStop,MemoryDiff" . PHP_EOL;
        
        $checkpoints = $this->_getProfiler()->getCheckpoints();
        $application = null;
        $indexLength = strlen(count($checkpoints));
        
        foreach ($checkpoints as $index => $checkpoint)
        {
            if ($index == 0) {
                $application = $checkpoint;
            }
            
            $table .= $this->_getRow(
                str_pad($index, $indexLength, '0', STR_PAD_LEFT),
                $checkpoint,
                $application
            );
        }
        
        $table .= "" . PHP_EOL;
        
        return $table;
    }
    
    /**
     * @access protected
     * @param  integer $index
     * @param  Profiler_Checkpoint $checkpoint
     * @param  Integernia_Profiler_Checkpoint $application
     * @return string
     */
    protected function _getRow($index, Profiler_Checkpoint $checkpoint,
                               Profiler_Checkpoint $application)
    {
        $profiler       = $this->_getProfiler();
        $divisor        = $profiler->getDivisor();
        $divisorSign    = $profiler->getDivisorSign();
        
        $appStartTime   = $application->startTime;
        $appStopTime    = $application->stopTime;
        
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