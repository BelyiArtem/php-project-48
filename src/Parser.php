<?php

namespace Hexlet\Code;

use InvalidArgumentException;

function parse(string $path): array
{
    if (!file_exists($path)) {
        throw new InvalidArgumentException("File '$path' not found.");
    }

    $fileContent = file_get_contents($path);

    return json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
}
