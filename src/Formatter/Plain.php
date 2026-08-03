<?php

namespace Hexlet\Code\Formatter;

use const Hexlet\Code\STATUS_NESTED;
use const Hexlet\Code\STATUS_REMOVED;
use const Hexlet\Code\STATUS_ADDED;
use const Hexlet\Code\STATUS_CHANGED;
use const Hexlet\Code\STATUS_UNCHANGED;

function plain(array $tree, string $path = ''): string
{
    $formatedTree = array_reduce($tree, function ($lines, $node) use ($path) {
        $item = match ($node['type']) {
            STATUS_NESTED => formatPlainNested($node, $path),
            STATUS_REMOVED => formatPlainRemoved($node, $path),
            STATUS_ADDED => formatPlainAdded($node, $path),
            STATUS_CHANGED => formatPlainChanged($node, $path),
            STATUS_UNCHANGED => []
        };

        return array_merge($lines, $item);
    }, []);

    return implode("\n", $formatedTree);
}

function formatPlainChanged(array $node, string $property): array
{
    $property .= $node['key'];
    $oldValue = toPlainValue($node['oldValue']);
    $newValue = toPlainValue($node['newValue']);

    return ["Property '$property' was updated. From $oldValue to $newValue"];
}

function formatPlainRemoved(array $node, string $property): array
{
    $property .= $node['key'];
    return ["Property '$property' was removed"];
}

function formatPlainAdded(array $node, string $property): array
{
    $property .= $node['key'];
    return ["Property '$property' was added with value: " . toPlainValue($node['value'])];
}

function formatPlainNested(array $node, string $property): array
{
    $property .= $node['key'] . '.';
    $result = plain($node['children'], $property);

    return $result === '' ? [] : explode("\n", $result);
}

function toPlainValue(mixed $value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }

    if (is_string($value)) {
        return "'$value'";
    }

    return toString($value);
}
