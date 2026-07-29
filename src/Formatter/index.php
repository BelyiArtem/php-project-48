<?php

namespace Hexlet\Code\Formatter;

use InvalidArgumentException;

use function Hexlet\Code\Formatter\stylish;

const INDENT_SIZE = 4;

function format(array $tree, string $format = 'stylish'): string
{
    return match ($format) {
        'stylish' => stylish($tree),
        default => throw new InvalidArgumentException("Format $format is not supported!"),
    };
}

function stringify($value, int $depth = 1): string
{
    $iter = function ($currentValue, $depth) use (&$iter) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentWidth = getIndent($depth);
        $currentIndent = indent($indentWidth);
        $bracketIndent = indent($indentWidth - INDENT_SIZE);

        $lines = array_map(
            fn($key, $val) => "$currentIndent$key: {$iter($val, $depth + 1)}",
            array_keys($currentValue),
            $currentValue
        );

        $result = ['{', ...$lines, "$bracketIndent}"];

        return implode("\n", $result);
    };

    return $iter($value, $depth);
}

function toString(mixed $value): string
{
    if ($value === null) {
        return 'null';
    }

    return trim(var_export($value, true), "'");
}

function indent(int $count): string
{
    return str_repeat(' ', $count);
}

function getIndent(int $depth): int
{
    return INDENT_SIZE * $depth;
}

function getSignIndent(int $depth): int
{
    return INDENT_SIZE * $depth - 2;
}
