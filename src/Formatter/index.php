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

function stringify($value, string $replacer = ' ', int $indentWidth = 4): string
{
    $iter = function ($currentValue, $depth) use (&$iter, $replacer, $indentWidth) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentSize = $depth * $indentWidth;
        $currentIndent = str_repeat($replacer, $indentSize);
        $bracketIndent = str_repeat($replacer, $indentSize - $indentWidth);

        $lines = array_map(
            fn($key, $val) => "$currentIndent$key: {$iter($val, $depth + 1)}",
            array_keys($currentValue),
            $currentValue
        );

        $result = ['{', ...$lines, "$bracketIndent}"];

        return implode("\n", $result);
    };

    return $iter($value, 1);
}

function toString(mixed $value): string
{
    if ($value === null) {
        return 'null';
    }

    return trim(var_export($value, true), "'");
}
