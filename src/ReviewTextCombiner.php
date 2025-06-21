<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner;

use ReviewCombiner\Output\IOutputItem;
use ReviewCombiner\Output\PlainLine;
use ReviewCombiner\Output\IssueRelatedLines;

final class ReviewTextCombiner
{
    /** @var IOutputItem[] */
    private array $linesList = [];

    private bool $isAfterEmptyLine = false;

    public function __construct(
        private readonly IssueDetector $issueDetector,
    ) {}

    public function reset()
    {
        $this->linesList = [];
        $this->isAfterEmptyLine = false;
    }

    public function addInputLine(string $line): void
    {
        $line = $this->normalizeLine($line);
        $isEmptyLine = $this->isEmptyLine($line);
        $lineIssue = $this->issueDetector->getIssue($line);
        if ($lineIssue) {
            if (!isset($this->linesList[$lineIssue])) {
                $this->linesList[$lineIssue] = new IssueRelatedLines($lineIssue);
            }
            $this->linesList[$lineIssue]->addLine($line);
        } elseif (!$isEmptyLine) {
            $this->linesList[] = new PlainLine($line);
        }

        $this->isAfterEmptyLine = $isEmptyLine;
    }

    private function normalizeLine(string $line): string
    {
        $line = rtrim($line);
        return $line;
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
                    fn (IOutputItem $item): string => $item->toOutputString(),
                    $this->linesList,
                )
            ) . "\n";

        return $result;
    }

}
