<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner\Output;

interface IOutputItem
{
    public function toOutputString(): string;
}