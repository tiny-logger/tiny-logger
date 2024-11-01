<?php

declare(strict_types=1);

namespace Serhii\TinyLogger;

use Curl\Curl;
use Exception;

final class Logger
{
    /**
     * @var string|null
     */
    private $file_path;

    /**
     * @var array<int, mixed>|null
     */
    private $post_request_json;

    /**
     * @var string|null
     */
    private $post_request_url;

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
     * @return self
     */
    public static function new(): self
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
     * @return Logger
     */
    public static function setPath(string $path, ...$params): Logger
    {
        self::new()->file_path = $params ? sprintf($path, ...$params) : $path;
        return self::new();
    }

    public static function getPath(): ?string
    {
        return self::new()->file_path;
    }

    /**
     * @param string $url endpoint where POST request is going to point
     * @param array<int, mixed>|null $json If this argument is passed, POST data is going to be a json object with
     * custom structure. You can name json fields and organize values however you want.
     */
    public static function enablePostRequest(string $url, ?array $json = null): void
    {
        self::new()->post_request_url = $url;
        self::new()->post_request_json = $json;
    }

    public static function disablePostRequest(): void
    {
        self::new()->post_request_url = null;
        self::new()->post_request_json = null;
    }

    /**
     * You can pass almost any type as the first argument and method will
     * figure out what it needs to do with this data in order to save it
     * into a file.
     *
     * @param mixed $input Text that will be written as a context. Can be any type.
     * If Throwable object is passed, it will be logged with whole stack trace,
     * error message and line number.
     * @param string|null $options Options can be log type like "error",
     * "debug", "warning" etc... Also you can pass option "pos".
     * To pass both option and log type separate them with pipe character
     * like that: "pos|info".
     * @param string|null $file_path
     *
     * @throws Exception Throws if file path wasn't provided by setPath()
     * method. Make sure that setPath() is called before the logging happens.
     */
    public function write($input, ?string $options = null, ?string $file_path = null): void
    {
        $self = self::new();
        $text = new Text($input);
        $option = new Option($options ?? 'error');

        $self->createFileIfNotExist();
        $self->makePostRequestIfOptionIsEnabled($text, $option, new Curl());

        $result = $self->prepareTextForLogging($text, $option);

        if ($file_path || $self->file_path) {
            file_put_contents($file_path ?? $self->file_path ?? '', $result, FILE_APPEND);
        }
    }

    private function makePostRequestIfOptionIsEnabled(Text $text, Option $option, Curl $curl): void
    {
        $self = self::new();

        if (!$self->post_request_url) {
            return;
        }

        $handler = new CurlHandler($self->post_request_url, $self->post_request_json, $text, $option, $curl);
        $handler->makeRequest();
    }

    /**
     * @throws Exception Throws if file path wasn't provided by setPath() method.
     */
    private function createFileIfNotExist(): void
    {
        if ($this->file_path === null) {
            throw new Exception('File path for logging output is not specified');
        }

        if (!file_exists($this->file_path)) {
            file_put_contents($this->file_path, '');
        }
    }

    private function prepareTextForLogging(Text $text, Option $option): string
    {
        $result = "{$text->getDateBlock()} {$option->getErrorType()}: {$text->getPreparedText()}" . PHP_EOL;

        if ($option->has('pos')) {
            return $result . $text->getTraceLine();
        }

        return $result;
    }

    /**
     * @param mixed $message
     *
     * @throws Exception
     */
    public function emergency($message): void
    {
        self::new()->write($message, 'emergency');
    }

    /**
     * @param mixed $message
     *
     * @throws Exception
     */
    public function alert($message): void
    {
        self::new()->write($message, 'alert');
    }

    /**
     * @param mixed $message
     *
     * @throws Exception
     */
    public function critical($message): void
    {
        self::new()->write($message, 'critical');
    }

    /**
     * @param mixed $message
     *
     * @throws Exception
     */
    public function error($message): void
    {
        self::new()->write($message, 'error');
    }

    /**
     * @param mixed $message
     *
     * @throws Exception
     */
    public function warning($message): void
    {
        self::new()->write($message, 'warning');
    }

    /**
     * @param mixed $message
     *
     * @throws Exception
     */
    public function notice($message): void
    {
        self::new()->write($message, 'notice');
    }

    /**
     * @param mixed $message
     *
     * @throws Exception
     */
    public function info($message): void
    {
        self::new()->write($message, 'info');
    }

    /**
     * @param mixed $message
     *
     * @throws Exception
     */
    public function debug($message): void
    {
        self::new()->write($message, 'debug');
    }
}
