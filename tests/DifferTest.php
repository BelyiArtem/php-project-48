<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\parse;
use function Differ\Differ\compare;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    #[DataProvider('formatProvider')]
    public function testParse(string $format): void
    {
        $simpleFile = parse($this->getFixtureFullPath("$format/file1.$format"));
        $nestedFile = parse($this->getFixtureFullPath("$format/file2.$format"));

        $expectedParse1 = json_decode(
            file_get_contents($this->getFixtureFullPath("expected/parse1.json")),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $expectedParse2 = json_decode(
            file_get_contents($this->getFixtureFullPath("expected/parse2.json")),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertEquals($expectedParse1, $simpleFile);
        $this->assertEquals($expectedParse2, $nestedFile);
    }

    #[DataProvider('formatProvider')]
    public function testCompareFunction(string $format): void
    {
        $simpleFile = parse($this->getFixtureFullPath("$format/file1.$format"));
        $nestedFile = parse($this->getFixtureFullPath("$format/file2.$format"));
        $expected = json_decode(
            file_get_contents($this->getFixtureFullPath("expected/compare.json")),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertEquals($expected, compare($simpleFile, $nestedFile));
    }

    #[DataProvider('fileProvider')]
    public function testGenDiff(string $inputFormat, string $outputFormat, string $expectedFixture): void
    {
        $simpleFile = $this->getFixtureFullPath("$inputFormat/file1.$inputFormat");
        $nestedFile = $this->getFixtureFullPath("$inputFormat/file2.$inputFormat");
        $actualString = genDiff($simpleFile, $nestedFile, $outputFormat);
        $expected = file_get_contents($this->getFixtureFullPath("expected/$expectedFixture"));

        $this->assertSame($expected, $actualString);
    }

    public function testHandleInvalidFile(): void
    {
        $wrongFile = $this->getFixtureFullPath('st1.json');
        $this->expectException(InvalidArgumentException::class);
        parse($wrongFile);
    }

    public static function fileProvider(): array
    {
        return [
            'json → stylish' => ['json', 'stylish', 'stylish.txt'],
            'yml → stylish'  => ['yml', 'stylish', 'stylish.txt'],

            'json → plain'   => ['json', 'plain', 'plain.txt'],
            'yml → plain'    => ['yml', 'plain', 'plain.txt'],

            'json → json'    => ['json', 'json', 'json.txt'],
            'yml → json'     => ['yml', 'json', 'json.txt'],
        ];
    }

    public static function formatProvider(): array
    {
        return [
            'json' => ['json'],
            'yml' => ['yml'],
        ];
    }

    private function getFixtureFullPath(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return implode('/', $parts);
    }
}
