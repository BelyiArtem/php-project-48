<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\readFile;
use function Differ\compare;
use function Differ\genDiff;

use const Differ\STATUS_NESTED;
use const Differ\STATUS_REMOVED;
use const Differ\STATUS_ADDED;
use const Differ\STATUS_CHANGED;
use const Differ\STATUS_UNCHANGED;

class DifferTest extends TestCase
{
    #[DataProvider('compareDataProvider')]
    public function testCompare(array $firstData, array $secondData, array $expected): void
    {
        $this->assertSame($expected, compare($firstData, $secondData));
    }

    #[DataProvider('fileProvider')]
    public function testGenDiff(string $inputFormat, string $outputFormat, string $expectedFixture): void
    {
        $firstFile = $this->getFixtureFullPath("$inputFormat/file1.$inputFormat");
        $secondFile = $this->getFixtureFullPath("$inputFormat/file2.$inputFormat");

        $this->assertStringEqualsFile(
            $this->getFixtureFullPath("expected/$expectedFixture"),
            genDiff($firstFile, $secondFile, $outputFormat)
        );
    }

    #[DataProvider('formatProvider')]
    public function testDefaultGenDiff(string $format): void
    {
        $firstFile = $this->getFixtureFullPath("$format/file1.$format");
        $secondFile = $this->getFixtureFullPath("$format/file2.$format");

        $this->assertStringEqualsFile(
            $this->getFixtureFullPath("expected/stylish.txt"),
            genDiff($firstFile, $secondFile)
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
        ];
    }

    public static function formatProvider(): array
    {
        return [
            ['json'],
            ['yml'],
        ];
    }

    public static function compareDataProvider(): array
    {
        return [
            'added property' => [
                ['foo' => 'bar'],
                ['foo' => 'bar', 'baz' => 'qux'],
                [
                    [
                        'key' => 'baz',
                        'type' => STATUS_ADDED,
                        'value' => 'qux',
                    ],
                    [
                        'key' => 'foo',
                        'type' => STATUS_UNCHANGED,
                        'value' => 'bar',
                    ],
                ],
            ],

            'removed property' => [
                ['foo' => 'bar', 'baz' => 'qux'],
                ['foo' => 'bar'],
                [
                    [
                        'key' => 'baz',
                        'type' => STATUS_REMOVED,
                        'value' => 'qux',
                    ],
                    [
                        'key' => 'foo',
                        'type' => STATUS_UNCHANGED,
                        'value' => 'bar',
                    ],
                ],
            ],

            'changed property' => [
                ['foo' => 'bar'],
                ['foo' => 'baz'],
                [
                    [
                        'key' => 'foo',
                        'type' => STATUS_CHANGED,
                        'oldValue' => 'bar',
                        'newValue' => 'baz',
                    ],
                ],
            ],

            'nested property' => [
                ['common' => ['foo' => 'bar']],
                ['common' => ['foo' => 'baz']],
                [
                    [
                        'key' => 'common',
                        'type' => STATUS_NESTED,
                        'children' => [
                            [
                                'key' => 'foo',
                                'type' => STATUS_CHANGED,
                                'oldValue' => 'bar',
                                'newValue' => 'baz',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getFixtureFullPath(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return implode('/', $parts);
    }
}
