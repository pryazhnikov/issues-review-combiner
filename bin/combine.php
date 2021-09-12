#!/usr/bin/env php
<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */

class IssueDetector
{
    public function getIssue(string $text): ?string
    {
        // "* Hello" -> "Hello"
        $cleanLine = preg_replace('#^[\s*\-]*#', '', $text);
        // "(Done)" => ""
        $cleanLine = preg_replace('#^\(([^)]+)\)#', '', $cleanLine);
        // "[WIP]" -> ""
        $cleanLine = preg_replace('#^\[(\]+)\]#', '', $cleanLine);

        // Lines to match: "SRV-123" / "* SRV-123" / " -  SRV-123"
        if (preg_match('#^\s*([A-Z]+-\d+)\s*#i', $cleanLine, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}

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


class ReviewTextCombiner
{
    private $linesList = [];

    private $isAfterEmptyLine = false;

    /** @var \IssueDetector */
    private $issueDetector;

    public function __construct(IssueDetector $issueDetector)
    {
        $this->issueDetector = $issueDetector;
    }

    public function reset()
    {
        $this->linesList = [];
        $this->isAfterEmptyLine = false;
    }

    public function addInputLine(string $line): void
    {
        $isEmptyLine = $this->isEmptyLine($line);
        $lineIssue = $this->issueDetector->getIssue($line);
        if ($lineIssue) {
            /**
             * All lines related to the same issue will be aggregated and written together.
             * Key is used to prevent the adding of the same lines of text.
             */
            $this->linesList[$lineIssue][$line] = $line;
        } elseif (!$isEmptyLine) {
            /**
             * Empty lines from input will be ignored and replaced by issue splitters.
             * @see getOutputText()
             */
            $this->linesList[] = $line;
        }

        $this->isAfterEmptyLine = $isEmptyLine;
    }

    private function isEmptyLine(string $line): bool
    {
        return ('' === trim($line));
    }

    public function getOutputText(): string
    {
        $result = implode(
            "\n",
            array_map(
                function ($item) {
                    return $this->getOutputItemString($item);
                },
                $this->linesList
            )
        ) . "\n";

        return $result;
    }

    /**
     * @param array|string $item
     * @return string
     */
    private function getOutputItemString($item): string
    {
        if (is_array($item)) {
            $result = implode('', $item);
            if (count($item) > 1) {
                $result = ("\n" . $result);
            }
        } else {
            $result = $item;
        }

        return $result;
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

