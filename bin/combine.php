#!/usr/bin/env php
<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
require(__DIR__ . "/../vendor/autoload.php");

use \ReviewCombiner\IssueDetector;
use \ReviewCombiner\ReviewTextCombiner;

function testIssueDetector(IssueDetector $detector): void
{
    // Good testcase: "* SRV-45684 (In progress) Clean unused debug logs"
    $testCases = [
        '' => null,
        "\t\t" => null,
        "* (On production) SRVLAB-1234 Code change notifications rollout" => 'SRVLAB-1234',
        ' - SRV-12321 (Code review) Hidden staffpass login should not automatically' => 'SRV-12321',
        '* (Other) Restarting data filling in the service' => null,
    ];

    $index = 0;
    foreach ($testCases as $line => $expectedIssue) {
        assert($expectedIssue === $detector->getIssue($line), "Issue detector test case #{$index} failed!");
        $index++;
    }
}

function testReviewTextCombiner(ReviewTextCombiner $combiner): void
{
    // @todo implement me
    $inputText = '(Done)

* SRV-49229 Add relaxations for non empty responses

(WIP)

* SRV-49229 Add relaxations for non empty responses';
}

/**
 * CLI script code
 */
$issueDetector = new IssueDetector();
testIssueDetector($issueDetector);

$combiner = new ReviewTextCombiner($issueDetector);
testReviewTextCombiner($combiner);

while ($line = fgets(STDIN)) {
    $combiner->addInputLine($line);
}

print $combiner->getOutputText();

