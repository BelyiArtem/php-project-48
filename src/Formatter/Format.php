<?php

namespace Differ\Differ\Formatter;

use InvalidArgumentException;

use function Differ\Differ\Formatter\stylish;
use function Differ\Differ\Formatter\plain;

const INDENT_SIZE = 4;

function format(array $tree, string $format = 'stylish'): string
{
    return match ($format) {
        'stylish' => stylish($tree),
        'plain' => plain($tree),
        'json' => json($tree),
        default => throw new InvalidArgumentException("Format $format is not supported!"),
    };
}

function toString(mixed $value): string
{
    if (is_string($value)) {
        return $value;
    }

    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        $value === null => 'null',
        default => (string) $value,
    };
}
