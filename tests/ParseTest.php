<?php
use PHPUnit\Framework\TestCase;
use function Hexlet\Code\parse;
use Exception;

class ParseTest extends TestCase
{
    public function testParser(): void
    {
        $file1 = __DIR__ . "/../media/file1.json";
        $this->assertTrue(is_array(parse($file1)));
    }

    public function testHandleParserErrors(): void
    {
        $file = "/src/file1.json";
        $this->expectException(Exception::class);
        parse($file);
    }
}
