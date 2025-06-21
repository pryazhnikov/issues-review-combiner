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

    /** @var IssueRelatedLines[] */
    private array $issueOutputItems = [];

    private bool $isAfterEmptyLine = false;

    public function __construct(
        private readonly IssueDetector $issueDetector,
    ) {}

    public function reset()
    {
        $this->linesList = [];
        $this->issueOutputItems = [];
        $this->isAfterEmptyLine = false;
    }

    public function addInputLine(string $line): void
    {
        $line = $this->normalizeLine($line);
        $isEmptyLine = $this->isEmptyLine($line);
        $lineIssue = $this->issueDetector->getIssue($line);
        if ($lineIssue) {
            /**
             * All lines related to the same issue will be aggregated and written together.
             */
            if (!isset($this->issueOutputItems[$lineIssue])) {
                $this->issueOutputItems[$lineIssue] = new IssueRelatedLines($lineIssue);
                $this->linesList[] = $this->issueOutputItems[$lineIssue];
            }

            $this->issueOutputItems[$lineIssue]->addLine($line);
        } elseif (!$isEmptyLine) {
            /**
             * Empty lines from input will be ignored and replaced by issue splitters.
             * @see self::getIssueSplitter()
             */
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
                $this->getIssueSplitter(),
                array_map(
                    fn (IOutputItem $item): string => $item->toOutputString(),
                    $this->linesList,
                )
            ) . $this->getIssueSplitter();

        return $result;
    }

    private function getIssueSplitter(): string
    {
        return PHP_EOL;
    }
}
