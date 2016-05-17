<?php

/**
 * ZendLCARS API
 *
 * @category  ZendLCARS API
 * @package   Profiler
 * @copyright Copyright (c) 2009 Bjoern Bartels, <info@dragon-projects.net>
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Profiler_Writer_Abstract
 */
require_once 'Profiler/Writer/Abstract.php';

/**
 * @category  ZendLCARS API
 * @package   Profiler
 * @author    Bjoern Bartels <info@dragon-projects.net>
 * @copyright Copyright (c) 2009 Bjoern Bartels, <info@dragon-projects.net>
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Profiler_Writer_HtmlTable
    extends Profiler_Writer_Abstract
{
    /**
     * @access public
     * @return string
     */
    public function get()
    {
        $table = "<table class=\"Profiler\" cellspacing=\"3\" cellpadding=\"3\" style=\"color: #000000; font-family: 'Verdana', 'Arial', 'Helvetica'; font-size: 10px; border: 0px dotted #00ffff; width: 100%;\" border=\"0\" >\n"
               . "    <thead>\n"
               . "        <tr>\n"
               . "            <th class=\"spacer\" style=\"background-color: #ff9900;\" colspan=\"2\"></th>\n"
               . "            <th class=\"time\" style=\"background-color: #ff9900;\" colspan=\"3\">Time</th>\n"
               . "            <th class=\"memory\" style=\"background-color: #ff9966;\" colspan=\"3\">Memory</th>\n"
               . "        </tr>\n"
               . "        <tr>\n"
               . "            <th></th><!-- <th class=\"index\" style=\"background-color: #f7c64a;\">#</th -->\n"
               . "            <th></th><!-- th class=\"title\" style=\"background-color: #f7c64a;\">Title</th -->\n"
               . "            <th class=\"time start\" style=\"background-color: #9999cc;\">Start</th>\n"
               . "            <th class=\"time stop\" style=\"background-color: #9999ff;\">Stop</th>\n"
               . "            <th class=\"time diff\" style=\"background-color: #cc99cc;\">Difference</th>\n"
               . "            <th class=\"memory start\" style=\"background-color: #ffcc99;\">Start</th>\n"
               . "            <th class=\"memory stop\" style=\"background-color: #ff9966;\">Stop</th>\n"
               . "            <th class=\"memory diff\" style=\"background-color: #cc99cc;\">Difference</th>\n"
               . "        </tr>\n"
               . "    </thead>\n"
               . "    <tbody>\n";
        
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
        
        $table .= "    </tbody>\n"
                . "</table>\n";
        
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
        
        $indention      = str_repeat('&nbsp;', $checkpoint->depth * 4);
        
        $row = "        <tr>\n"
             . "            <th class=\"index\" style=\"background-color: #f7c64a; text-align: right;\">{$index}</th>\n"
             . "            <td class=\"title\" style=\"background-color: #f7c64a;\">{$indention}{$checkpoint->title}</td>\n"
             . "            <td class=\"time start\" style=\"background-color: #9999cc; text-align: right;\">{$startTime}s</td>\n"
             . "            <td class=\"time stop\" style=\"background-color: #9999ff; text-align: right;\">{$stopTime} s</td>\n"
             . "            <td class=\"time diff\" style=\"background-color: #cc99cc; text-align: right;\">{$diffTime} s</td>\n"
             . "            <td class=\"memory start\" style=\"background-color: #ffcc99; text-align: right;\">{$startMemory}</td>\n"
             . "            <td class=\"memory stop\" style=\"background-color: #ff9966; text-align: right;\">{$stopMemory}</td>\n"
             . "            <td class=\"memory diff\" style=\"background-color: #cc99cc; text-align: right;\">{$diffMemory}</td>\n"
             . "        </tr>\n";
        
        return $row;
    }
    
}