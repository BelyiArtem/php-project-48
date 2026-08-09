<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\parse;
use function Differ\Differ\readFile;
use function Differ\Differ\compare;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    #[DataProvider('formatProvider')]
    public function testCompare(string $format): void
    {
        [$firstContent, $firstExtension] = readFile($this->getFixtureFullPath("$format/file1.$format"));
        [$secondContent, $secondExtension] = readFile($this->getFixtureFullPath("$format/file2.$format"));

        $firstData = parse($firstContent, $firstExtension);
        $secondData = parse($secondContent, $secondExtension);
        $expected = json_decode(
            file_get_contents($this->getFixtureFullPath("expected/compare.json")),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertEquals($expected, compare($firstData, $secondData));
    }

    #[DataProvider('fileProvider')]
    public function testGenDiff(string $inputFormat, ?string $outputFormat, string $expectedFixture): void
    {
        $firstFile = $this->getFixtureFullPath("$inputFormat/file1.$inputFormat");
        $secondFile = $this->getFixtureFullPath("$inputFormat/file2.$inputFormat");

        $actual = $outputFormat === null
            ? genDiff($firstFile, $secondFile)
            : genDiff($firstFile, $secondFile, $outputFormat);

        $this->assertStringEqualsFile(
            $this->getFixtureFullPath("expected/$expectedFixture"),
            $actual
        );
    }

    public function testHandleInvalidFile(): void
    {
        $wrongFile = $this->getFixtureFullPath('st1.json');
        $this->expectException(InvalidArgumentException::class);
        readFile($wrongFile);
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

            'json -> default' => ['json', null, 'stylish.txt'],
            'yml -> default' => ['yml', null, 'stylish.txt'],
        ];
    }

    public static function formatProvider(): array
    {
        return [
            ['json'],
            ['yml'],
        ];
    }

    private function getFixtureFullPath(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return implode('/', $parts);
    }
}
