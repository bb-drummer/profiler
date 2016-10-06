<?php
/**
 * phpunit bootstrap
 *
 * @package   Tests
 * @author    Björn Bartels <coding@bjoernbartels.earth>
 * @link      https://gitlab.bjoernbartels.earth/groups/php
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @copyright copyright (c) 2007 Björn Bartels <coding@bjoernbartels.earth>
 */

chdir(__DIR__);
$previousDir = '.';
while (!is_dir($previousDir . DIRECTORY_SEPARATOR . 'vendor')) {
    $appRoot = dirname(getcwd());

    if ($previousDir === $appRoot) {
        throw new RuntimeException('Unable to locate application root');
    }

    $previousDir = $appRoot;
    chdir($appRoot);
}

// Load composer autoloader
require_once $appRoot . '/vendor/autoload.php';