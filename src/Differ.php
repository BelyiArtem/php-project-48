<?php

namespace Hexlet\Code;

use function Hexlet\Code\parse;
use function Hexlet\Code\Formatter\format;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{
    $firstData = parse($firstFile);
    $secondData = parse($secondFile);

    $tree = compare($firstData, $secondData);

    return format($tree, $format);
}

function compare(array $firstData, array $secondData): array
{
    $sortedKeys = sortKeys(array_unique(array_merge(
        array_keys($firstData),
        array_keys($secondData)
    )));

    return array_reduce($sortedKeys, function ($acc, $key) use ($firstData, $secondData) {
        $firstExists = array_key_exists($key, $firstData);
        $secondExists = array_key_exists($key, $secondData);

        if (!$firstExists || !$secondExists) {
            $acc[] = [
                'key' => $key,
                'type' => $firstExists ? STATUS_REMOVED : STATUS_ADDED,
                'value' => $firstExists ? $firstData[$key] : $secondData[$key],
            ];

            return $acc;
        }

        $oldValue = $firstData[$key];
        $newValue = $secondData[$key];

        if (is_array($oldValue) && is_array($newValue)) {
            $acc[] = [
                'key' => $key,
                'type' => STATUS_NESTED,
                'children' => compare($oldValue, $newValue),
            ];

            return $acc;
        }

        if ($oldValue === $newValue) {
            $acc[] = [
                'key' => $key,
                'type' => STATUS_UNCHANGED,
                'value' => $oldValue,
            ];

            return $acc;
        }

        $acc[] = [
            'key' => $key,
            'type' => STATUS_CHANGED,
            'oldValue' => $oldValue,
            'newValue' => $newValue,
        ];

        return $acc;
    }, []);
}

function sortKeys(array $keys): array
{
    $sorted = $keys;
    usort($sorted, function ($curr, $next) {
        return mb_strtolower($curr) <=> mb_strtolower($next);
    });

    return $sorted;
}
