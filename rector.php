<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets(php81: true)
    ->withPreparedSets(
        deadCode: true,
        typeDeclarations: true,
        privatization: true,
        codeQuality: true
    );
