<?php

declare(strict_types=1);

namespace Serhii\TinyLogger;

use Exception;

final class Logger
{
    /**
     * @var string|null
     */
    private $file_path;

    /**
     * @var self|null
     */
    private static $instance;

    private function __construct()
    {
    }

    /**
     * Get singleton instance of the class.
     *
     * @see https://en.wikipedia.org/wiki/Singleton_pattern
     * @return \Serhii\TinyLogger\Logger
     */
    public static function singleton(): Logger
    {
        return self::$instance ?? (self::$instance = new self());
    }

    /**
     * Set path for logging output, if file doesn't exists it will be created.
     * It will not create directories, so make sure you have directories in
     * the provided path.
     *
     * @param string $path Absolute or relative path to a directory where log file
     * will be created. Sprintf syntax is allowed for this method like so:
     * setPath('%s/storage/logs/logs.log', '/var/www/html')

     * @param mixed ...$params
     *
     * @return \Serhii\TinyLogger\Logger
     */
    public static function setPath(string $path, ...$params): Logger
    {
        $instance = self::singleton();
        $instance->file_path = $params ? \sprintf($path, ...$params) : $path;
        return $instance;
    }

    /**
     * You can pass almost any type as the first argument and method will
     * figure out what it needs to do with this data in order to save it
     * into a file.
     *
     * @param mixed $text Text that will be written as a context. Can be any type.
     * If Throwable object is passed, it will be logged with whole stack trace,
     * error message and line number.
     * @param string|null $options Options can be log type like "error",
     * "debug", "warning" etc... Also you can pass option "pos".
     * To pass both option and log type separate them with pipe character
     * like that: "pos|info".
     *
     * @throws \Exception Throws if file path wasn't wasn't provided by setPath()
     * method. Make sure that setPath() is called before the logging happens.
     */
    public function write($text, ?string $options = 'error'): void
    {
        $instance = self::singleton();
        $instance->createFileIfNotExist();
        $input = $instance->prepareTextForLogging(new Text($text), new Option($options ?? 'error'));

        if ($instance->file_path) {
            \file_put_contents($instance->file_path, $input, FILE_APPEND);
        }
    }

    /**
     * @throws \Exception Throws if file path wasn't wasn't provided by setPath() method.
     */
    private function createFileIfNotExist(): void
    {
        if (\is_null($this->file_path)) {
            throw new Exception('File path for logging output is not specified');
        }

        if (!\file_exists($this->file_path)) {
            \file_put_contents($this->file_path, '');
        }
    }

    private function prepareTextForLogging(Text $text, Option $option): string
    {
        $text->prepare();
        $option->prepare();

        $result = "{$text->getDateBlock()} {$option->getErrorType()}: {$text->getPreparedText()}" . PHP_EOL;

        if ($option->has('pos')) {
            return $result . $text->getTraceLine();
        }

        return $result;
    }

    /**
     * @param mixed $message
     *
     * @throws \Exception
     */
    public function emergency($message): void
    {
        self::singleton()->write($message, 'emergency');
    }

    /**
     * @param mixed $message
     *
     * @throws \Exception
     */
    public function alert($message): void
    {
        self::singleton()->write($message, 'alert');
    }

    /**
     * @param mixed $message
     *
     * @throws \Exception
     */
    public function critical($message): void
    {
        self::singleton()->write($message, 'critical');
    }

    /**
     * @param mixed $message
     *
     * @throws \Exception
     */
    public function error($message): void
    {
        self::singleton()->write($message, 'error');
    }

    /**
     * @param mixed $message
     *
     * @throws \Exception
     */
    public function warning($message): void
    {
        self::singleton()->write($message, 'warning');
    }

    /**
     * @param mixed $message
     *
     * @throws \Exception
     */
    public function notice($message): void
    {
        self::singleton()->write($message, 'notice');
    }

    /**
     * @param mixed $message
     *
     * @throws \Exception
     */
    public function info($message): void
    {
        self::singleton()->write($message, 'info');
    }

    /**
     * @param mixed $message
     *
     * @throws \Exception
     */
    public function debug($message): void
    {
        self::singleton()->write($message, 'debug');
    }
}
