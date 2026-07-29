<?php

namespace Hexlet\Code;

const STATUS_ADDED = 'added';
const STATUS_REMOVED = 'removed';
const STATUS_CHANGED = 'changed';
const STATUS_UNCHANGED = 'unchanged';
const STATUS_NESTED = 'nested';

function compare(array $firstData, array $secondData): array
{
    $sortedKeys = sortKeys(array_unique(array_merge(
        array_keys($firstData),
        array_keys($secondData)
    )));

    return array_map(
        fn($key) => buildNode($key, $firstData, $secondData),
        $sortedKeys
    );
}

function buildNode(string $key, array $firstData, array $secondData): array
{
    $firstExists = array_key_exists($key, $firstData);
    $secondExists = array_key_exists($key, $secondData);

    if (!$firstExists || !$secondExists) {
        return buildAddedOrRemovedNode($key, $firstExists, $firstData, $secondData);
    }

    return buildExistingNode($key, $firstData, $secondData);
}

function buildAddedOrRemovedNode(string $key, bool $firstExists, array $firstData, array $secondData): array
{
    return [
        'key' => $key,
        'type' => $firstExists ? STATUS_REMOVED : STATUS_ADDED,
        'value' => $firstExists ? $firstData[$key] : $secondData[$key],
    ];
}

function buildExistingNode(string $key, array $firstData, array $secondData): array
{
    $oldValue = $firstData[$key];
    $newValue = $secondData[$key];

    if (is_array($oldValue) && is_array($newValue)) {
        return [
            'key' => $key,
            'type' => STATUS_NESTED,
            'children' => compare($oldValue, $newValue),
        ];
    }

    if ($oldValue === $newValue) {
        return [
            'key' => $key,
            'type' => STATUS_UNCHANGED,
            'value' => $oldValue,
        ];
    }

    return [
        'key' => $key,
        'type' => STATUS_CHANGED,
        'oldValue' => $oldValue,
        'newValue' => $newValue,
    ];
}

function sortKeys(array $keys): array
{
    $sorted = $keys;
    usort($sorted, function ($curr, $next) {
        return mb_strtolower($curr) <=> mb_strtolower($next);
    });

    return $sorted;
}
