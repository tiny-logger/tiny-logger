<?php

declare(strict_types=1);

use Serhii\TinyLogger\Logger;

if (!function_exists('tiny_log')) {
    /**
     * @param mixed $message
     * @param string|null $log_type
     * @param string|null $file_path
     * @throws \Exception
     */
    function tiny_log($message, ?string $log_type = 'error', ?string $file_path = null): void
    {
        $logger = Logger::new();
        $logger->write($message, $log_type, $file_path);
    }
}
