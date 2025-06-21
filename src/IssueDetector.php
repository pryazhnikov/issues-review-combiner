<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */
namespace ReviewCombiner;

final class IssueDetector
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
        } else if(preg_match('#\.atlassian\.net/jira[\/\w\d?=&]+&selectedIssue=([A-Z]+-\d+)#i', $cleanLine, $match)) {
            return $match[1];
        } else if(preg_match('#\.atlassian\.net/browse/([A-Z]+-\d+)#i', $cleanLine, $match)) {
            return $match[1];
        } else if(preg_match('#youtrack\.jetbrains\.com/issue/([A-Z]+-\d+)#i', $cleanLine, $match)) {
            return $match[1];
        } else if(preg_match('#redmine\.org/issues/(\d+)#i', $cleanLine, $match)) {
            return $match[1];
        } else if(preg_match('#github\.com/[\w_-]+/[\w_-]+/issues/(\d+)#i', $cleanLine, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}