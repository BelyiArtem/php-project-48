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
