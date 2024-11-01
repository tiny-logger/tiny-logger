<?php

declare(strict_types=1);

namespace Serhii\TinyLogger;

use Throwable;

final class Text
{
    /**
     * @var mixed
     */
    private $input_text;

    /**
     * @param mixed $input_text
     */
    public function __construct($input_text)
    {
        $this->input_text = $input_text;
    }

    public function getPreparedText(): string
    {
        if ($this->input_text instanceof Throwable) {
            $e = $this->input_text;
            $trace = $e->getTraceAsString();
            return "{$e->getMessage()} in {$e->getFile()} at line: {$e->getLine()}\n{$trace}";
        }

        if (is_array($this->input_text) || is_object($this->input_text)) {
            $encoded = json_encode($this->input_text, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return is_string($encoded) ? $encoded : '';
        }

        if (is_float($this->input_text) || is_int($this->input_text)) {
            return (string) $this->input_text;
        }

        if ($this->input_text === true) {
            return 'true';
        }

        if ($this->input_text === false) {
            return 'false';
        }

        return $this->input_text ?? 'null';
    }

    public function getTraceLine(): string
    {
        $trace = debug_backtrace()[2];

        if (preg_match('!/logger\.php!', $trace['file'] ?? '')) {
            $trace = debug_backtrace()[3];
        }

        $line = $trace['line'] ?? '';
        $file = $trace['file'] ?? '';

        return ">>> {$file} on line: {$line}" . PHP_EOL;
    }

    public function getDateBlock(?bool $timestamp = null): string
    {
        return $timestamp ? (string) time() : '[' . date('Y-m-d H:i:s') . ']';
    }
}
