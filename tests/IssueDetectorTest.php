<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner;

use PHPUnit\Framework\TestCase;

class IssueDetectorTest extends TestCase
{
    public function providerGetIssue(): array
    {
        return [
            'Empty case #1' => ['', null],
            'Empty case #2' => ["\t\t", null],
            'Empty case #3' => ['* (Other) Restarting data filling in the service', null],
            ["* (On production) SRVLAB-1234 Code change notifications rollout", 'SRVLAB-1234'],
            [' - SRV-12321 (Code review) Hidden staffpass login should not automatically', 'SRV-12321'],
        ];
    }

    /**
     * @dataProvider providerGetIssue
     *
     * @param string $line
     * @param string|null $expectedIssue
     */
    public function testGetIssue(string $line, ?string $expectedIssue): void
    {
        $detector = new IssueDetector();
        $actualIssue = $detector->getIssue($line);
        $this->assertEquals($expectedIssue, $actualIssue, "Wrong issue code was found!");
    }
}