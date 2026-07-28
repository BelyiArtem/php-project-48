<?php

namespace Hexlet\Code;

use function Hexlet\Code\parse;
use function Hexlet\Code\Formatter\format;

function genDiff(string $firstFile, string $secondFile): string
{
    $firstData = parse($firstFile);
    $secondData = parse($secondFile);

    $tree = compare($firstData, $secondData);

    return format($tree);
}

function compare(array $firstData, array $secondData): array
{
    $firstDataKeys = array_keys($firstData);
    $secondDataKeys = array_keys($secondData);
    $dataKeys = array_unique(array_merge($firstDataKeys, $secondDataKeys));

    $sortedKeys = sortKeys($dataKeys);

    return array_reduce($sortedKeys, function ($acc, $key) use ($firstData, $secondData) {
        $firstKeyExists = array_key_exists($key, $firstData);
        $secondKeyExists = array_key_exists($key, $secondData);

        if ($firstKeyExists && $secondKeyExists) {
            $dataValue1 = $firstData[$key];
            $dataValue2 = $secondData[$key];
            if (is_array($dataValue1) && is_array($dataValue2)) {
                $acc[] = [
                    'key' => $key,
                    'type' => STATUS_NESTED,
                    'children' => compare($dataValue1, $dataValue2)
                ];

                return $acc;
            }

            if ($dataValue1 !== $dataValue2) {
                $acc[] = [
                    'key' => $key,
                    'type' => STATUS_CHANGED,
                    'oldValue' => $dataValue1,
                    'newValue' => $dataValue2,
                ];
            } else {
                $acc[] = [
                    'key' => $key,
                    'type' => STATUS_UNCHANGED,
                    'value' => $dataValue1
                ];
            }
        } else {
            $acc[] = [
                'key' => $key,
                'type' => $firstKeyExists ? STATUS_REMOVED : STATUS_ADDED,
                'value' => $firstKeyExists ? $firstData[$key] : $secondData[$key]
            ];
        }

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
