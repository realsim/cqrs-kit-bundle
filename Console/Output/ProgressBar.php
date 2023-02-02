<?php

namespace Mechanic\CqrsKit\Console\Output;

use Symfony\Component\Console\Helper\ProgressBar as Helper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ProgressBar
{
    private const FORMAT = ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%';
    private const FORMAT_NOMAX = ' %current% [%bar%] %elapsed:6s% %memory:6s%';

    private OutputInterface $output;
    private Helper $helper;

    public function __construct(OutputInterface $output, int $max = 0)
    {
        $this->output = $output;

        if ($this->output instanceof SymfonyStyle) {
            $this->helper = $this->output->createProgressBar($max);
        } else {
            $this->helper = new Helper($this->output, $max);
        }

        $this->setFormat(false);
    }

    private function setFormat(bool $withTitle): void
    {
        $format = $this->helper->getMaxSteps() > 0 ? self::FORMAT : self::FORMAT_NOMAX;
        if (true === $withTitle) {
            $format = ' <fg=cyan>%title%</>'.PHP_EOL.$format;
        }

        $this->helper->setFormat($format);
    }

    public function setTitle(string $title)
    {
        $this->helper->setMessage($title, 'title');
        $this->setFormat(true);
        $this->helper->display();
    }

    public function start(int $max = 0)
    {
        $this->helper->start($max);
    }

    public function reset()
    {
        $this->helper->setProgress(0);
    }

    public function advance(int $step = 1)
    {
        $this->helper->advance($step);
    }

    public function pause()
    {
        $this->helper->clear();
    }

    public function resume()
    {
        $this->helper->display();
    }

    public function finish()
    {
        $this->helper->finish();
        $this->output->write(str_repeat(PHP_EOL, 2));
    }
}
