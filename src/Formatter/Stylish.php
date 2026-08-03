<?php

namespace Differ\Differ\Formatter;

use const Differ\Differ\STATUS_NESTED;
use const Differ\Differ\STATUS_REMOVED;
use const Differ\Differ\STATUS_ADDED;
use const Differ\Differ\STATUS_CHANGED;
use const Differ\Differ\STATUS_UNCHANGED;

function stylish(array $tree, int $depth = 1): string
{
    $formatedTree = array_reduce($tree, function ($lines, $node) use ($depth) {
        $item = match ($node['type']) {
            STATUS_NESTED => formatNested($node, $depth),
            STATUS_REMOVED => formatRemoved($node, $depth),
            STATUS_ADDED => formatAdded($node, $depth),
            STATUS_CHANGED => formatChanged($node, $depth),
            STATUS_UNCHANGED => formatUnchanged($node, $depth),
        };

        return array_merge($lines, $item);
    }, []);
    $indent = indent(getIndent($depth - 1));
    $result = ['{', ...$formatedTree, "$indent}"];

    return implode("\n", $result);
}

function formatChanged(array $node, int $depth): array
{
    $line = [];
    $indent = indent(getSignIndent($depth));

    if (array_key_exists('oldValue', $node) && array_key_exists('newValue', $node)) {
        $line[] = "$indent- {$node['key']}: " . stringify($node['oldValue'], $depth + 1);
        $line[] = "$indent+ {$node['key']}: " . stringify($node['newValue'], $depth + 1);

        return $line;
    }

    $line[] = "$indent- {$node['key']}: " . stringify($node['value'], $depth + 1);

    return $line;
}

function formatRemoved(array $node, int $depth): array
{
    $indent = indent(getSignIndent($depth));

    return [
        "$indent- {$node['key']}: " . stringify($node['value'], $depth + 1)
    ];
}

function formatUnchanged(array $node, int $depth): array
{
    $indent = indent(getIndent($depth));

    return [
        "$indent{$node['key']}: " . stringify($node['value'], $depth + 1)
    ];
}

function formatAdded(array $node, int $depth): array
{
    $indent = indent(getSignIndent($depth));

    return [
        "$indent+ {$node['key']}: " . stringify($node['value'], $depth + 1)
    ];
}

function formatNested(array $node, int $depth): array
{
    $indent = indent(getIndent($depth));

    return [
        "$indent{$node['key']}: " . stylish($node['children'], $depth + 1)
    ];
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
