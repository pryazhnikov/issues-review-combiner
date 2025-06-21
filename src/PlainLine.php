<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner;

readonly final class PlainLine
{
    public function __construct(
        private string $line
    ) {}

    public function toOutputString(): string
    {
        return $this->line . PHP_EOL;
    }
}