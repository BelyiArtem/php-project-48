<?php

namespace Differ\Formatters\Json;

function render(array $tree): string
{
    return json_encode($tree, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
}
