<?php

namespace Differ\Differ\Formatter;

function json(array $tree): string
{
    return json_encode($tree, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
}
