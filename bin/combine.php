#!/usr/bin/env php
<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
require(__DIR__ . "/../vendor/autoload.php");

use Symfony\Component\Console\Application;
use ReviewCombiner\Commands\CombineCommand;

$application = new Application('Review Text Combiner', '1.0.0');
$application->add(new CombineCommand());
$application->setDefaultCommand('combine', true);
$application->run();
