#!/usr/bin/env php
<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
require(__DIR__ . "/../vendor/autoload.php");

use \ReviewCombiner\IssueDetector;
use \ReviewCombiner\ReviewTextCombiner;

$issueDetector = new IssueDetector();
$combiner = new ReviewTextCombiner($issueDetector);

while ($line = fgets(STDIN)) {
    $combiner->addInputLine($line);
}

print $combiner->getOutputText();
