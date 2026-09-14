<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Function_\AddFunctionVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/tests',
        __DIR__.'/database',
        __DIR__.'/routes',
        __DIR__.'/config',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
    )
    ->withPhpSets()
    ->withComposerBased(laravel: true)
    ->withSkip([
        SafeDeclareStrictTypesRector::class,
        AddVoidReturnTypeWhereNoReturnRector::class,
        AddFunctionVoidReturnTypeWhereNoReturnRector::class,
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        // Mis-fires on Blade::component()->aliasComponent() and breaks the view compiler
        RenameMethodRector::class,
    ]);
