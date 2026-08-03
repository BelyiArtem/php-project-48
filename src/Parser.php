<?php

namespace Hexlet\Code;

use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

function parse(string $path): array
{
    if (!file_exists($path)) {
        throw new InvalidArgumentException("File '$path' not found.");
    }

    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    return match ($extension) {
        'json' => json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR),
        'yml', 'yaml' => Yaml::parseFile($path),
        default => throw new InvalidArgumentException("Format '$extension' is not supported."),
    };
}
