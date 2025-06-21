<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner\Output;

readonly final class PlainLine implements IOutputItem
{
    public function __construct(
        private string $line
    ) {}

    public function toOutputString(): string
    {
        return $this->line . PHP_EOL;
    }
}
