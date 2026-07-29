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
        $simpleFile = parse($this->getFixtureFullPath("$format/file.$format"));
        $nestedFile = parse($this->getFixtureFullPath("$format/file_nested.$format"));

        $this->assertEquals([
            "ASC" => 139,
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
            "follow" => false,
            "tls" => true,
            "default" => [
                "http" => false,
                "trace" => null
            ]
        ], $simpleFile);

        $this->assertEquals([
            "asc" => 139,
            "host" => "common.io",
            "timeout" => 150,
            "proxy" => "100.200.00.22",
            "common" => [
                "ip" => "192.168.0.1",
                "port" => 445
            ],
            "tcp" => true,
            "tls" => true,
            "default" => [
                "http" => false,
                "trace" => null
            ]
        ], $nestedFile);
    }

    #[DataProvider('fileProvider')]
    public function testCompareFunction(string $format): void
    {
        $simpleFile = parse($this->getFixtureFullPath("$format/file.$format"));
        $nestedFile = parse($this->getFixtureFullPath("$format/file_nested.$format"));
        $expectedArray = [
            [
                'key' => 'ASC',
                'type' => STATUS_REMOVED,
                'value' => 139
            ],
            [
                'key' => 'asc',
                'type' => STATUS_ADDED,
                'value' => 139
            ],
            [
                'key' => 'common',
                'type' => STATUS_ADDED,
                'value' => [
                    "ip" => "192.168.0.1",
                    "port" => 445
                ]
            ],
            [
                'key' => 'default',
                'type' => STATUS_NESTED,
                'children' => [
                    [
                        'key' => 'http',
                        'type' => STATUS_UNCHANGED,
                        'value' => false
                    ],
                    [
                        'key' => 'trace',
                        'type' => STATUS_UNCHANGED,
                        'value' => null
                    ]
                ]
            ],
            [
                'key' => 'follow',
                'type' => STATUS_REMOVED,
                'value' => false
            ],
            [
                'key' => 'host',
                'type' => STATUS_CHANGED,
                'oldValue' => "hexlet.io",
                "newValue" => "common.io"
            ],
            [
                'key' => 'proxy',
                'type' => STATUS_CHANGED,
                'oldValue' => "123.234.53.22",
                'newValue' => "100.200.00.22"
            ],
            [
                'key' => 'tcp',
                'type' => STATUS_ADDED,
                'value' => true
            ],
            [
                'key' => 'timeout',
                'type' => STATUS_CHANGED,
                'oldValue' => 50,
                'newValue' => 150
            ],
            [
                'key' => 'tls',
                'type' => STATUS_UNCHANGED,
                'value' => true
            ],
        ];

        $this->assertEquals($expectedArray, compare($simpleFile, $nestedFile));
    }

    #[DataProvider('fileProvider')]
    public function testGenDiff(string $format): void
    {
        $simpleFile = $this->getFixtureFullPath("$format/file.$format");
        $nestedFile = $this->getFixtureFullPath("$format/file_nested.$format");
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
