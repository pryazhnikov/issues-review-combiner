<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner\Output;

final class IssueRelatedLines implements IOutputItem
{
    private array $lines = [];

    public function __construct(
        private readonly string $issueCode
    ) {}

    public function addLine(string $line): void
    {
        // Key is used to prevent the adding of the same lines of text.
        // Actually we need an ordered set here, but PHP offers a sorted associative array instead
        $this->lines[$line] = $line;
    }

    public function toOutputString(): string
    {
        return implode(PHP_EOL, $this->lines) . PHP_EOL;
    }
}
