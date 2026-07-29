<?php

namespace Hexlet\Code\Formatter;

use const Hexlet\Code\STATUS_NESTED;
use const Hexlet\Code\STATUS_REMOVED;
use const Hexlet\Code\STATUS_ADDED;
use const Hexlet\Code\STATUS_CHANGED;
use const Hexlet\Code\STATUS_UNCHANGED;

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

    if (array_key_exists('oldValue', $node) && array_key_exists('newValue',$node)) {
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
