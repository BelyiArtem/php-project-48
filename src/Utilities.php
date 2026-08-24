<?php

namespace Differ\Utilities;

use InvalidArgumentException;

function readFile(string $path): array
{
    if (!file_exists($path)) {
        throw new InvalidArgumentException("File '$path' not found.");
    }

    return [
        file_get_contents($path),
        strtolower(pathinfo($path, PATHINFO_EXTENSION)),
    ];
}

function toString(mixed $value): string
{
    if (is_string($value)) {
        return $value;
    }

    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        $value === null => 'null',
        default => (string) $value,
    };
}
