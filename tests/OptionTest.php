<?php

declare(strict_types=1);

namespace Serhii\Tests;

use PHPUnit\Framework\TestCase;
use Serhii\TinyLogger\Logger;

class OptionTest extends TestCase
{
    public $file_name = 'logs-test.log';

    public function setUp(): void
    {
        Logger::setPath($this->file_name);
    }

    public function tearDown(): void
    {
        file_exists($this->file_name) ? unlink($this->file_name) : null;
    }


    public function testPosOptionAddsTraceLineToLogFile(): void
    {
        Logger::new()->write('Nice text is here', 'pos|debug');
        $line_number = __LINE__ - 1;

        $log_file_content = file_get_contents($this->file_name);

        $regex = sprintf('!>>> %s on line: %d!', __FILE__, $line_number);
        $this->assertRegExp($regex, $log_file_content);
    }


    public function testIfPosOptionNotProvidedTraceLineIsNotAdded(): void
    {
        Logger::new()->write('Nice text is here', 'debug');
        $line_number = __LINE__ - 1;

        $log_file_content = file_get_contents($this->file_name);
        $regex = sprintf('!>>> %s on line: %d!', __FILE__, $line_number);

        $this->assertNotRegExp($regex, $log_file_content);
        $this->assertNotRegExp('!>>>!', $log_file_content);
    }


    public function testPosOptionAddsTraceLineToLogFileWhenUsingFunction(): void
    {
        tiny_log('Nice text is here', 'pos|debug');
        $line_number = __LINE__ - 1;

        $log_file_content = file_get_contents($this->file_name);

        $regex = sprintf('!>>> %s on line: %d!', __FILE__, $line_number);
        $this->assertRegExp($regex, $log_file_content);
    }


    public function testIfPosOptionNotProvidedTraceLineIsNotAddedWhenUsingFunction(): void
    {
        tiny_log('Nice text is here', 'debug');
        $line_number = __LINE__ - 1;

        $log_file_content = file_get_contents($this->file_name);
        $regex = sprintf('!>>> %s on line: %d!', __FILE__, $line_number);

        $this->assertNotRegExp($regex, $log_file_content);
        $this->assertNotRegExp('!>>>!', $log_file_content);
    }


    public function testYouCanPassOptionWithoutErrorType(): void
    {
        tiny_log('Nice text is here', 'pos');
        $line_number = __LINE__ - 1;

        $log_file_content = file_get_contents($this->file_name);

        $regex = sprintf('!>>> %s on line: %d!', __FILE__, $line_number);
        $this->assertRegExp($regex, $log_file_content);
    }
}
