<?php

namespace Differ\Differ;

const STATUS_ADDED = 'added';
const STATUS_REMOVED = 'removed';
const STATUS_CHANGED = 'changed';
const STATUS_UNCHANGED = 'unchanged';
const STATUS_NESTED = 'nested';

function compare(array $firstData, array $secondData): array
{
    $keys = array_unique(array_merge(array_keys($firstData), array_keys($secondData)));
    $sortedKeys  = array_values(
        sortBy($keys, fn($key) => mb_strtolower($key))
    );

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

function sortBy(array $collection, callable|string $sortBy, string $sortFunction = 'asort'): array
{
    if (!is_callable($sortBy)) {
        $sortBy = static fn ($item) => $item[$sortBy];
    }

    $values = array_map($sortBy, $collection);

    $sortFunction($values);

    $result = [];

    foreach (array_keys($values) as $key) {
        $result[$key] = $collection[$key];
    }

    return $result;
}
