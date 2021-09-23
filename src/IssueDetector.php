<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner;

class IssueDetector
{
    public function getIssue(string $text): ?string
    {
        // "* Hello" -> "Hello"
        $cleanLine = preg_replace('#^[\s*\-]*#', '', $text);
        // "(Done)" => ""
        $cleanLine = preg_replace('#^\(([^)]+)\)#', '', $cleanLine);
        // "[WIP]" -> ""
        $cleanLine = preg_replace('#^\[(\]+)\]#', '', $cleanLine);

        // Lines to match: "SRV-123" / "* SRV-123" / " -  SRV-123"
        if (preg_match('#^\s*([A-Z]+-\d+)\s*#i', $cleanLine, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}