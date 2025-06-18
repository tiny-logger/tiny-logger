<?php

declare(strict_types=1);

namespace Serhii\Tests;

use function Arokettu\Debug\call_private_method;

use Curl\Curl;
use Error;
use Exception;
use ParseError;
use PHPUnit\Framework\TestCase;
use Serhii\TinyLogger\Logger;
use Serhii\TinyLogger\Option;
use Serhii\TinyLogger\Text;
use TypeError;

class LoggerTest extends TestCase
{
    public $file_path = 'logs-test.log';

    public function setUp(): void
    {
        Logger::setPath($this->file_path);
    }

    public function tearDown(): void
    {
        $this->removeFile($this->file_path);
        Logger::disablePostRequest();
    }

    private function removeFile(string $file_path): void
    {
        file_exists($file_path) ? unlink($file_path) : null;
    }


    public function testWriteMethodCreatesLogFile(): void
    {
        $this->assertFileNotExists($this->file_path);
        Logger::new()->write('Some log message goes here');
        $this->assertFileExists($this->file_path);
    }


    public function testSetPathChangesFilePath(): void
    {
        Logger::setPath('different.log')->write('Some log message goes here');
        $this->assertFileExists('different.log');
        file_exists('different.log') ? unlink('different.log') : null;
    }


    public function testYouCanUseSprintfSyntaxIntInsetPathMethod(): void
    {
        Logger::setPath('%serent%s', 'diff', '.log')->write('Some log message goes here');
        $this->assertFileExists('different.log');
        file_exists('different.log') ? unlink('different.log') : null;
    }


    public function testWriteMethodWritesGivenTextToALogFile(): void
    {
        Logger::new()->write('Nice text is here');
        Logger::new()->write(null);
        $log_file_content = file_get_contents($this->file_path);
        $this->assertStringContainsString('] error: Nice text is here', $log_file_content);
        $this->assertStringContainsString('null', $log_file_content);
    }


    public function testWriteMethodWritesGivenTextToALogFileWithDifferentType(): void
    {
        Logger::new()->write('Nice text is here', 'debug');
        $log_file_content = file_get_contents($this->file_path);
        $this->assertStringContainsString('] debug: Nice text is here', $log_file_content);
    }


    public function testWriteMethodCanExceptArray(): void
    {
        $array = ['hello' => 'world'];
        Logger::new()->write($array, 'info');

        $log_file_content = file_get_contents($this->file_path);

        $json = json_encode($array, JSON_PRETTY_PRINT);
        $this->assertStringContainsString($json, $log_file_content);
    }


    public function testWriteMethodCanExceptObject(): void
    {
        $obj = (object) ['hello' => 'world'];
        Logger::new()->write($obj, 'info');

        $log_file_content = file_get_contents($this->file_path);

        $json = json_encode($obj, JSON_PRETTY_PRINT);
        $this->assertStringContainsString($json, $log_file_content);
    }


    public function testFunctionCreatesLogFile(): void
    {
        $this->assertFileNotExists($this->file_path);
        tiny_log('Some log message goes here');
        $this->assertFileExists($this->file_path);
    }


    public function testLoggerHasMethodHelpers(): void
    {
        $this->assertFileNotExists($this->file_path);
        Logger::new()->error('Some log message goes here');
        $this->assertFileExists($this->file_path);

        $instance = Logger::new();

        $this->assertTrue(method_exists($instance, 'emergency'));
        $this->assertTrue(method_exists($instance, 'alert'));
        $this->assertTrue(method_exists($instance, 'critical'));
        $this->assertTrue(method_exists($instance, 'error'));
        $this->assertTrue(method_exists($instance, 'warning'));
        $this->assertTrue(method_exists($instance, 'notice'));
        $this->assertTrue(method_exists($instance, 'info'));
        $this->assertTrue(method_exists($instance, 'debug'));
    }


    public function testWriteMethodCanExceptException(): void
    {
        try {
            throw new Exception('This is an exception');
        } catch (Exception $e) {
            Logger::new()->write($e);
        }

        $log_file_content = file_get_contents($this->file_path);

        $this->assertStringContainsString('This is an exception', $log_file_content);
    }


    public function testWriteMethodCanExceptError(): void
    {
        try {
            throw new Error('This is an error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $log_file_content = file_get_contents($this->file_path);

        $this->assertStringContainsString('This is an error', $log_file_content);
    }


    public function testWriteMethodCanExceptParseError(): void
    {
        try {
            throw new ParseError('This is a parse error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $this->assertStringContainsString('This is a parse error', file_get_contents($this->file_path));
    }


    public function testWriteMethodCanExceptTypeError(): void
    {
        try {
            throw new TypeError('This is a type error');
        } catch (Error $e) {
            Logger::new()->write($e);
        }

        $this->assertStringContainsString('This is a type error', file_get_contents($this->file_path));
    }


    public function testWriteMethodCanExceptBooleanTrue(): void
    {
        Logger::new()->write(true, 'info');
        $this->assertStringContainsString('true', file_get_contents($this->file_path));
    }


    public function testWriteMethodCanExceptBooleanFalse(): void
    {
        Logger::new()->write(false, 'info');
        $log_file_content = file_get_contents($this->file_path);
        $this->assertStringContainsString('false', $log_file_content);
    }


    public function testWriteMethodCanExceptNull(): void
    {
        Logger::new()->write(null, 'info');
        $log_file_content = file_get_contents($this->file_path);
        $this->assertStringContainsString('null', $log_file_content);
    }


    public function testMakePostRequestIfOptionIsEnabledMakesRequestIfOptionIsEnabled(): void
    {
        $text = new Text('Error message');
        $option = new Option('info');

        $curl = $this->createMock(Curl::class);

        $curl->expects($this->once())->method('setHeader');
        $curl->expects($this->once())->method('post');

        Logger::enablePostRequest('http://my-site.io/web-hook');

        call_private_method(Logger::new(), 'makePostRequestIfOptionIsEnabled', $text, $option, $curl);
    }


    public function testMakePostRequestIfOptionIsEnabledDoesntMakeRequestIfOptionIsNotEnabled(): void
    {
        $text = new Text('My error message');
        $option = new Option('error');

        $curl = $this->createMock(Curl::class);

        $curl->expects($this->never())->method('setHeader');
        $curl->expects($this->never())->method('post');

        call_private_method(Logger::new(), 'makePostRequestIfOptionIsEnabled', $text, $option, $curl);
    }


    public function testDisablePostRequestDisablesPostRequestAfterOptionHasBeenEnabledEnabled(): void
    {
        $text = new Text('Some error message');
        $option = new Option('debug');

        $curl = $this->createMock(Curl::class);

        $curl->expects($this->never())->method('setHeader');
        $curl->expects($this->never())->method('post');

        Logger::enablePostRequest('http://site.io/web-hook');
        Logger::disablePostRequest();

        call_private_method(Logger::new(), 'makePostRequestIfOptionIsEnabled', $text, $option, $curl);
    }


    public function testYouCanPassFilePathAsTheThirdArgumentInFunctionAndItWillNotChangeGlobalFilePath(): void
    {
        $file_path = 'nice.log';

        tiny_log('Text', 'info', $file_path);
        $this->assertSame($this->file_path, Logger::getPath());

        $this->removeFile($file_path);
    }


    public function testYouCanPassFilePathAsTheThirdArgumentInWriteMethodAndItWillNotChangeGlobalFilePath(): void
    {
        $file_path = 'nice.log';

        Logger::new()->write('Text', 'info', $file_path);
        $this->assertSame($this->file_path, Logger::getPath());

        $this->removeFile($file_path);
    }


    public function testGetPathMethodReturnsFilePath(): void
    {
        $this->assertSame($this->file_path, Logger::getPath());
    }
}
