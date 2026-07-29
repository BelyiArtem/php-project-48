<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Hexlet\Code\parse;
use function Hexlet\Code\compare;
use function Hexlet\Code\genDiff;

use const Hexlet\Code\STATUS_NESTED;
use const Hexlet\Code\STATUS_REMOVED;
use const Hexlet\Code\STATUS_ADDED;
use const Hexlet\Code\STATUS_CHANGED;
use const Hexlet\Code\STATUS_UNCHANGED;

class GenDiffTest extends TestCase
{
    #[DataProvider('fileProvider')]
    public function testParse(string $format): void
    {
        $simpleFile = parse($this->getFixtureFullPath("$format/file1.$format"));
        $nestedFile = parse($this->getFixtureFullPath("$format/file2.$format"));

        $this->assertEquals([
            'common' => [
                'setting1' => 'Value 1',
                'setting2' => 200,
                'setting3' => true,
                'setting6' => [
                    'key' => 'value',
                    'doge' => [
                        'wow' => '',
                    ],
                ],
            ],
            'group1' => [
                'baz' => 'bas',
                'foo' => 'bar',
                'nest' => [
                    'key' => 'value',
                ],
            ],
            'group2' => [
                'abc' => 12345,
                'deep' => [
                    'id' => 45,
                ],
            ],
        ], $simpleFile);

        $this->assertEquals([
            'common' => [
                'follow' => false,
                'setting1' => 'Value 1',
                'setting3' => null,
                'setting4' => 'blah blah',
                'setting5' => [
                    'key5' => 'value5',
                ],
                'setting6' => [
                    'key' => 'value',
                    'ops' => 'vops',
                    'doge' => [
                        'wow' => 'so much',
                    ],
                ],
            ],
            'group1' => [
                'foo' => 'bar',
                'baz' => 'bars',
                'nest' => 'str',
            ],
            'group3' => [
                'deep' => [
                    'id' => [
                        'number' => 45,
                    ],
                ],
                'fee' => 100500,
            ],
        ], $nestedFile);
    }

    #[DataProvider('fileProvider')]
    public function testCompareFunction(string $format): void
    {
        $simpleFile = parse($this->getFixtureFullPath("$format/file1.$format"));
        $nestedFile = parse($this->getFixtureFullPath("$format/file2.$format"));
        $expectedArray = [
            [
                'key' => 'common',
                'type' => STATUS_NESTED,
                'children' => [
                    [
                        'key' => 'follow',
                        'type' => STATUS_ADDED,
                        'value' => false,
                    ],
                    [
                        'key' => 'setting1',
                        'type' => STATUS_UNCHANGED,
                        'value' => 'Value 1',
                    ],
                    [
                        'key' => 'setting2',
                        'type' => STATUS_REMOVED,
                        'value' => 200,
                    ],
                    [
                        'key' => 'setting3',
                        'type' => STATUS_CHANGED,
                        'oldValue' => true,
                        'newValue' => null,
                    ],
                    [
                        'key' => 'setting4',
                        'type' => STATUS_ADDED,
                        'value' => 'blah blah',
                    ],
                    [
                        'key' => 'setting5',
                        'type' => STATUS_ADDED,
                        'value' => [
                            'key5' => 'value5',
                        ],
                    ],
                    [
                        'key' => 'setting6',
                        'type' => STATUS_NESTED,
                        'children' => [
                            [
                                'key' => 'doge',
                                'type' => STATUS_NESTED,
                                'children' => [
                                    [
                                        'key' => 'wow',
                                        'type' => STATUS_CHANGED,
                                        'oldValue' => '',
                                        'newValue' => 'so much',
                                    ],
                                ],
                            ],
                            [
                                'key' => 'key',
                                'type' => STATUS_UNCHANGED,
                                'value' => 'value',
                            ],
                            [
                                'key' => 'ops',
                                'type' => STATUS_ADDED,
                                'value' => 'vops',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'group1',
                'type' => STATUS_NESTED,
                'children' => [
                    [
                        'key' => 'baz',
                        'type' => STATUS_CHANGED,
                        'oldValue' => 'bas',
                        'newValue' => 'bars',
                    ],
                    [
                        'key' => 'foo',
                        'type' => STATUS_UNCHANGED,
                        'value' => 'bar',
                    ],
                    [
                        'key' => 'nest',
                        'type' => STATUS_CHANGED,
                        'oldValue' => [
                            'key' => 'value',
                        ],
                        'newValue' => 'str',
                    ],
                ],
            ],
            [
                'key' => 'group2',
                'type' => STATUS_REMOVED,
                'value' => [
                    'abc' => 12345,
                    'deep' => [
                        'id' => 45,
                    ],
                ],
            ],
            [
                'key' => 'group3',
                'type' => STATUS_ADDED,
                'value' => [
                    'deep' => [
                        'id' => [
                            'number' => 45,
                        ],
                    ],
                    'fee' => 100500,
                ],
            ],
        ];

        $this->assertEquals($expectedArray, compare($simpleFile, $nestedFile));
    }

    #[DataProvider('fileProvider')]
    public function testGenDiff(string $format): void
    {
        $simpleFile = $this->getFixtureFullPath("$format/file1.$format");
        $nestedFile = $this->getFixtureFullPath("$format/file2.$format");
        $actualString = genDiff($simpleFile, $nestedFile);
        $expected = file_get_contents($this->getFixtureFullPath('expected_stylish.txt'));

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
            'json' => ['json'],
            'yml'  => ['yml'],
        ];
    }

    private function getFixtureFullPath(string $fixtureName): string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return implode('/', $parts);
    }
}
