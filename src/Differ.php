<?php

namespace Differ\Differ;

use function Differ\Differ\parse;
use function Differ\Differ\Formatter\format;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{
    $firstData = parse($firstFile);
    $secondData = parse($secondFile);

    $tree = compare($firstData, $secondData);

    return format($tree, $format);
}
