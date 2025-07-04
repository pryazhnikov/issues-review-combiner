<?php
/**
 * @author Victor Pryazhnikov <victor@pryazhnikov.com>
 */

namespace ReviewCombiner\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ReviewCombiner\IssueDetector;
use ReviewCombiner\ReviewTextCombiner;

class CombineCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('combine')
            ->setDescription('Reads text from the input, combines issues related texts and writes it into output')
            ->addOption('input', 'i', InputOption::VALUE_REQUIRED, 'Input file (default: STDIN)')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output file (default: STDOUT)')
            ->setHelp('This command reads input and combines issues related lines together');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $issueDetector = new IssueDetector();
        $combiner = new ReviewTextCombiner($issueDetector);

        $inputFile = $input->getOption('input');
        $outputFile = $input->getOption('output');

        $inputHandle = $inputFile ? fopen($inputFile, 'r') : STDIN;
        if (!$inputHandle) {
            $output->writeln('<error>Could not open input file: ' . $inputFile . '</error>');
            return Command::FAILURE;
        }

        while ($line = fgets($inputHandle)) {
            $combiner->addInputLine($line);
        }

        if ($inputFile) {
            fclose($inputHandle);
        }

        $result = $combiner->getOutputText();

        if ($outputFile) {
            if (file_put_contents($outputFile, $result) === false) {
                $output->writeln('<error>Could not write to output file: ' . $outputFile . '</error>');
                return Command::FAILURE;
            }
            $output->writeln('<info>Output written to: ' . $outputFile . '</info>');
        } else {
            $output->write($result);
        }

        return Command::SUCCESS;
    }
}