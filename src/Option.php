<?php

declare(strict_types=1);

namespace Serhii\TinyLogger;

final class Option
{
    public const AVAILABLE_OPTIONS = ['pos'];

    /**
     * @var string
     */
    private $input_options;

    public function __construct(string $input_options)
    {
        $this->input_options = $input_options;
    }

    public function getErrorType(): string
    {
        foreach (explode('|', $this->input_options) as $option) {
            if (!in_array($option, self::AVAILABLE_OPTIONS, true)) {
                return $option;
            }
        }

        return 'error';
    }

    public function has(string $option_name): bool
    {
        $options = [];

        foreach (explode('|', $this->input_options) as $option) {
            if (in_array($option, self::AVAILABLE_OPTIONS, true)) {
                $options[] = $option;
            }
        }

        return in_array($option_name, $options, true);
    }
}
