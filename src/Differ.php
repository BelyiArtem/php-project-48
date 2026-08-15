<?php

namespace Differ\Differ;

use InvalidArgumentException;

use function Differ\Formatter\format;
use function Differ\Parsers\parse;
use function Differ\TreeBuilder\compare;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{
    [$firstContent, $firstExtension] = readFile($firstFile);
    [$secondContent, $secondExtension] = readFile($secondFile);

    $firstData = parse($firstContent, $firstExtension);
    $secondData = parse($secondContent, $secondExtension);

    $tree = compare($firstData, $secondData);

    return format($tree, $format);
}

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
