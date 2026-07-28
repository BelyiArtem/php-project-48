<?php

namespace Hexlet\Code\Formatter;

use function Hexlet\Code\Formatter\stringify;

use const Hexlet\Code\STATUS_NESTED;
use const Hexlet\Code\STATUS_REMOVED;
use const Hexlet\Code\STATUS_ADDED;
use const Hexlet\Code\STATUS_CHANGED;
use const Hexlet\Code\STATUS_UNCHANGED;

function stylish(array $tree): string
{
    $lines = ["{"];
    $formatted = array_reduce($tree, function ($lines, $node) {
        $item = match ($node['type']) {
            STATUS_NESTED => formatNested($node),
            STATUS_REMOVED => formatRemoved($node),
            STATUS_ADDED => formatAdded($node),
            STATUS_CHANGED => formatChanged($node),
            STATUS_UNCHANGED => formatUnchanged($node),
        };

        return array_merge($lines, $item);
    }, []);
    $lines = array_merge($lines, $formatted);
    $lines[] = "}";

    return implode("\n", $lines);
}

function formatChanged(array $node): array
{
    $line = [];
    if (isset($node['oldValue']) && isset($node['newValue'])) {
        $line[] = "- {$node['key']}: " . stringify($node['oldValue']);
        $line[] = "+ {$node['key']}: " . stringify($node['newValue']);

        return $line;
    }

    $line[] = "- {$node['key']}: " . stringify($node['value']);

    return $line;
}

function formatRemoved(array $node): array
{
    return [
        "- {$node['key']}: " . stringify($node['value'])
    ];
}

function formatUnchanged(array $node): array
{
    return [
        "  {$node['key']}: " . stringify($node['value'])
    ];
}

function formatAdded(array $node): array
{
    return [
        "+ {$node['key']}: " . stringify($node['value'])
    ];
}

function formatNested(array $node): array
{
    return [
        "  {$node['key']}: " . stylish($node['children'])
    ];
}
