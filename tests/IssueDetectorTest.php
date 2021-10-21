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
            'Issue case #1' => ["* (On production) SRVLAB-1234 Code change notifications rollout", 'SRVLAB-1234'],
            'Issue case #2' => [' - SRV-12321 (Code review) Hidden staffpass login should not automatically', 'SRV-12321'],
            'Jira-URL case #1: long format line-end' => ['see https://test.atlassian.net/jira/software/c/projects/TE/boards/1?modal=detail&selectedIssue=TE-123', 'TE-123'],
            'Jira-URL case #2: long format more text' => ['see https://corporation.atlassian.net/jira/software/c/projects/UDX/boards/1?modal=detail&selectedIssue=UDX-145 for more details', 'UDX-145'],
            'Jira-URL case #3: short format line-end' => ['Refs: https://something.atlassian.net/browse/XY-456','XY-456'],
            'Jira-URL case #3: short format more text' => ['Refs: https://acme.atlassian.net/browse/ABC-6575 and other issues','ABC-6575'],
            'Youtrack-URL case #1' => ['https://youtrack.jetbrains.com/issue/KT-12345', 'KT-12345'],
            'RedMine-URL case #1' => ['https://www.redmine.org/issues/33333', '33333'],
            'GitHub-URL case #1' => ['https://github.com/pryazhnikov/issues-review-combiner/issues/123', '123'],
            'GitHub-URL case #2' => ['https://github.com/someuser/important_project/issues/456', '456'],
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