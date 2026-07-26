<?php

namespace Hexlet\Code\Formatter;

use InvalidArgumentException;

use function Hexlet\Code\Formatter\stylish;

function format(array $tree, string $format = 'stylish'): string
{
    return match ($format) {
        'stylish' => stylish($tree),
        default => throw new InvalidArgumentException("Format $format is not supported!"),
    };
}

function toString(mixed $value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if ($value === null) {
        return 'null';
    }

    return (string) $value;
}
