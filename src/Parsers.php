<?php

namespace Differ;

use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;

function parse(string $content, string $extension): array
{
    return match ($extension) {
        'json' => json_decode($content, true, 512, JSON_THROW_ON_ERROR),
        'yml', 'yaml' => Yaml::parse($content),
        default => throw new InvalidArgumentException("Format '$extension' is not supported."),
    };
}
