<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner;

final class ReviewTextCombiner
{
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
                    fn ($item): string => $this->getOutputItemString($item),
                    $this->linesList,
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
            $result = implode(PHP_EOL, $item);
        } else {
            $result = $item;
        }

        return $result . PHP_EOL;
    }
}
