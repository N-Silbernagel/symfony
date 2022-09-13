<?php

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;

final class MultiOutput implements OutputInterface
{
    private array $outputs;

    /** @param OutputInterface[] $outputs */
    public function __construct(array $outputs, ?int $verbosity = self::VERBOSITY_NORMAL, bool $decorated = false, OutputFormatterInterface $formatter = null)
    {
        $this->outputs = $this->array_clone($outputs);
        $verbosity ??= self::VERBOSITY_NORMAL;
        $formatter ??= new OutputFormatter();
        $this->formatter->setDecorated($decorated);

        foreach ($this->outputs as $output) {
            $output->setVerbosity($verbosity);
            $output->setFormatter($formatter);
            $output->setDecorated($decorated);
        }
    }

    public function write(string|iterable $messages, bool $newline = false, int $options = 0)
    {
        foreach ($this->outputs as $output) {
            $output->write($messages, $newline, $options);
        }
    }

    public function writeln(string|iterable $messages, int $options = 0)
    {
        foreach ($this->outputs as $output) {
            $output->write($messages, $options);
        }
    }

    public function getVerbosity(): int
    {
        return $this->outputs[0]->getVerbosity();
    }

    public function isQuiet(): bool
    {
        return self::VERBOSITY_QUIET === $this->verbosity;
    }

    public function isVerbose(): bool
    {
        return self::VERBOSITY_VERBOSE <= $this->verbosity;
    }

    public function isVeryVerbose(): bool
    {
        return self::VERBOSITY_VERY_VERBOSE <= $this->verbosity;
    }

    public function setVerbosity(int $level)
    {
        foreach ($this->outputs as $output) {
            $output->setVerbosity($level);
        }
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
        foreach ($this->outputs as $output) {
            $output->setFormatter($formatter);
        }
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return $this->outputs[0]->getFormatter();
    }

    public function setDecorated(bool $decorated)
    {
        $this->formatter->setDecorated($decorated);
    }

    public function isDebug(): bool
    {
        return self::VERBOSITY_DEBUG <= $this->verbosity;
    }

    private function array_clone($array)
    {
        return array_map(function ($element) {
            return ((is_array($element))
                ? $this->array_clone($element)
                : ((is_object($element))
                    ? clone $element
                    : $element
                )
            );
        }, $array);
    }
}
