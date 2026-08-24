<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Utilities\readFile;
use function Differ\TreeBuilder\compare;
use function Differ\Differ\genDiff;

use const Differ\TreeBuilder\STATUS_NESTED;
use const Differ\TreeBuilder\STATUS_REMOVED;
use const Differ\TreeBuilder\STATUS_ADDED;
use const Differ\TreeBuilder\STATUS_CHANGED;
use const Differ\TreeBuilder\STATUS_UNCHANGED;

class DifferTest extends TestCase
{
    #[DataProvider('compareDataProvider')]
    public function testCompare(array $firstData, array $secondData, array $expected): void
    {
        $this->assertSame($expected, compare($firstData, $secondData));
    }

    #[DataProvider('formatProvider')]
    public function testStylish(string $inputFormat): void
    {
        $firstFile = $this->getFixtureFullPath("$inputFormat/file1.$inputFormat");
        $secondFile = $this->getFixtureFullPath("$inputFormat/file2.$inputFormat");

        $this->assertStringEqualsFile(
            $this->getFixtureFullPath('expected/stylish.txt'),
            genDiff($firstFile, $secondFile, 'stylish')
        );
    }

    #[DataProvider('formatProvider')]
    public function testPlain(string $inputFormat): void
    {
        $firstFile = $this->getFixtureFullPath("$inputFormat/file1.$inputFormat");
        $secondFile = $this->getFixtureFullPath("$inputFormat/file2.$inputFormat");

        $this->assertStringEqualsFile(
            $this->getFixtureFullPath('expected/plain.txt'),
            genDiff($firstFile, $secondFile, 'plain')
        );
    }

    #[DataProvider('formatProvider')]
    public function testJson(string $inputFormat): void
    {
        $firstFile = $this->getFixtureFullPath("$inputFormat/file1.$inputFormat");
        $secondFile = $this->getFixtureFullPath("$inputFormat/file2.$inputFormat");

        $this->assertStringEqualsFile(
            $this->getFixtureFullPath('expected/json.txt'),
            genDiff($firstFile, $secondFile, 'json')
        );
    }

    #[DataProvider('formatProvider')]
    public function testDefault(string $inputFormat): void
    {
        $firstFile = $this->getFixtureFullPath("$inputFormat/file1.$inputFormat");
        $secondFile = $this->getFixtureFullPath("$inputFormat/file2.$inputFormat");

        $this->assertStringEqualsFile(
            $this->getFixtureFullPath('expected/stylish.txt'),
            genDiff($firstFile, $secondFile)
        );
    }

    public function testHandleInvalidFile(): void
    {
        $wrongFile = $this->getFixtureFullPath('st1.json');
        $this->expectException(InvalidArgumentException::class);
        readFile($wrongFile);
    }

    public static function formatProvider(): array
    {
        return [
            'json input' => ['json'],
            'yml input' => ['yml'],
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
