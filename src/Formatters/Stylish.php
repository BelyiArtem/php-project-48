<?php

namespace Differ\Formatters\Stylish;

use function Differ\Formatter\toString;

use const Differ\TreeBuilder\STATUS_NESTED;
use const Differ\TreeBuilder\STATUS_REMOVED;
use const Differ\TreeBuilder\STATUS_ADDED;
use const Differ\TreeBuilder\STATUS_CHANGED;
use const Differ\TreeBuilder\STATUS_UNCHANGED;

const INDENT_SIZE = 4;

function render(array $tree, int $depth = 1): string
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
    $indent = indent(getSignIndent($depth));
    $oldValue = stringify($node['oldValue'], $depth + 1);
    $newValue = stringify($node['newValue'], $depth + 1);

    return [
        "$indent- {$node['key']}: $oldValue",
        "$indent+ {$node['key']}: $newValue",
    ];
}

function formatRemoved(array $node, int $depth): array
{
    $indent = indent(getSignIndent($depth));
    $value = stringify($node['value'], $depth + 1);

    return ["$indent- {$node['key']}: $value"];
}

function formatUnchanged(array $node, int $depth): array
{
    $indent = indent(getIndent($depth));
    $value = stringify($node['value'], $depth + 1);

    return ["$indent{$node['key']}: $value"];
}

function formatAdded(array $node, int $depth): array
{
    $indent = indent(getSignIndent($depth));
    $value = stringify($node['value'], $depth + 1);

    return ["$indent+ {$node['key']}: $value"];
}

function formatNested(array $node, int $depth): array
{
    $indent = indent(getIndent($depth));
    $value = render($node['children'], $depth + 1);

    return ["$indent{$node['key']}: $value"];
}

function stringify($value, int $depth = 1): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    $indentWidth = getIndent($depth);
    $currentIndent = indent($indentWidth);
    $bracketIndent = indent($indentWidth - INDENT_SIZE);

    $lines = array_map(
        function ($key, $val) use ($currentIndent, $depth) {
            $value = stringify($val, $depth + 1);

            return "{$currentIndent}{$key}: {$value}";
        },
        array_keys($value),
        $value
    );

    return implode("\n", [
        '{',
        ...$lines,
        "{$bracketIndent}}",
    ]);
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
