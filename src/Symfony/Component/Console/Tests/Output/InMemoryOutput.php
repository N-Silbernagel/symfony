<?php

use Symfony\Component\Console\Output\Output;

final class InMemoryOutput extends Output
{
    /** @var string[] */
    private array $lines = [''];

    public function doWrite(string $message, bool $newline)
    {
        $this->lines[$this->currentLineIndex()] .= $message;

        if ($newline) {
            $this->lines[] = '';
        }
    }

    private function currentLineIndex(): int
    {
        return array_key_last($this->lines);
    }
}
