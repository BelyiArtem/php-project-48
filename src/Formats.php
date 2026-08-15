<?php

namespace Differ\Formatter;

use InvalidArgumentException;

use function Differ\Formatters\Json\render as renderJson;
use function Differ\Formatters\Plain\render as renderPlain;
use function Differ\Formatters\Stylish\render as renderStylish;

function format(array $tree, string $format = 'stylish'): string
{
    return match ($format) {
        'stylish' => renderStylish($tree),
        'plain' => renderPlain($tree),
        'json' => renderJson($tree),
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
