<?php

namespace Mechanic\CqrsKit\Console\Output;

use Symfony\Component\Console\Output\OutputInterface;

class ServiceOutput
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function info(string $message): void
    {
        $this->output->writeln(sprintf(' <fg=green>[%s] %s</>', 'INFO', $message));
    }

    public function debug(string $message): void
    {
        $this->output->writeln(sprintf(' [%s] %s', 'DEBUG', $message), OutputInterface::OUTPUT_NORMAL | OutputInterface::VERBOSITY_DEBUG);
    }

    public function startProgress(int $max = 0): ProgressBar
    {
        $progressBar = new ProgressBar($this->output, $max);
        $progressBar->start($max);

        return $progressBar;
    }
}
