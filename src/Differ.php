<?php

namespace Differ\Differ;

use function Differ\Formatter\format;
use function Differ\Parsers\parse;
use function Differ\TreeBuilder\compare;
use function Differ\Utilities\readFile;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{
    [$firstContent, $firstExtension] = readFile($firstFile);
    [$secondContent, $secondExtension] = readFile($secondFile);

    $firstData = parse($firstContent, $firstExtension);
    $secondData = parse($secondContent, $secondExtension);

    $tree = compare($firstData, $secondData);

    return format($tree, $format);
}
