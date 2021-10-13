<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner;

use PHPUnit\Framework\TestCase;

class ReviewTextCombinerTest extends TestCase
{
    private ReviewTextCombiner $combiner;

    protected function setUp(): void
    {
        parent::setUp();

        // This solution is not perfect, because we have a not mocked dependency
        // An alternative (using mock of IssueDetector) is not a best solution,
        // because we have to write a new implementation of the same logic.
        $this->combiner = new ReviewTextCombiner(new IssueDetector());
    }

    private function buildOutputText(string $input): string
    {
        // This code is an emulation of fgets() behaviour
        $line = '';
        $separator = "\n";
        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            $line .= $char;
            if ($char === $separator) {
                $this->combiner->addInputLine($line);
                $line = '';
            }
        }

        if (!empty($line)) {
            $this->combiner->addInputLine($line);
        }

        return $this->combiner->getOutputText();
    }

    public function testOutputMultipleIssueLines(): void
    {
        $input = <<<TEXT
            * SRV-42424 Add relaxations for non empty responses - initial version is ready
            
            * SRV-42424 Add relaxations for non empty responses - the fix was deployed
            TEXT;

        // Duplicated lines should be flattened
        $expectedOutput = <<<TEXT
            * SRV-42424 Add relaxations for non empty responses - initial version is ready
            * SRV-42424 Add relaxations for non empty responses - the fix was deployed
            \n
            TEXT;

        $actualOutput = $this->buildOutputText($input);

        $this->assertEquals($expectedOutput, $actualOutput, "Wrong output");
    }

    public function testOutputDuplicatedIssueLines(): void
    {
        /**
         * Two things to mention here:
         * 1. a whitespace character in the end of the first issue line
         * 2. the lack of an newline character after the second issue line
         */
        $input = <<<TEXT
            * SRV-42424 Add relaxations for non empty responses\t
            
            * SRV-42424 Add relaxations for non empty responses
            TEXT;

        // Duplicated lines should be flattened
        // T
        $expectedOutput = <<<TEXT
            * SRV-42424 Add relaxations for non empty responses
            \n
            TEXT;

        $actualOutput = $this->buildOutputText($input);

        $this->assertEquals($expectedOutput, $actualOutput, "Wrong output");
    }

    public function testOutputComplexText(): void
    {
        $input = <<<TEXT
            (Day #1)
            * (In progress) SRV-42424 Add relaxations for non empty responses
            * (Other) Investigation of some issues
            
            (Day #2)
            * (Done) SRV-42424 Add relaxations for non empty responses (part #2)
            * QA-12345 Improvements of unit tests
            * Preparations for the meetings
            * Discussion of something important
            TEXT;

        // Duplicated lines should be flattened
        $expectedOutput = <<<TEXT
            (Day #1)
            
            * (In progress) SRV-42424 Add relaxations for non empty responses
            * (Done) SRV-42424 Add relaxations for non empty responses (part #2)
            
            * (Other) Investigation of some issues
            
            (Day #2)
            
            * QA-12345 Improvements of unit tests
            
            * Preparations for the meetings
            
            * Discussion of something important
            \n
            TEXT;

        $actualOutput = $this->buildOutputText($input);

        $this->assertEquals($expectedOutput, $actualOutput, "Wrong output");
    }
}
