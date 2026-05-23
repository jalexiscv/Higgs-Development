<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__,
    ])
    ->name('*.php')
    ->exclude([
        'vendor',
        'node_modules',
        '.git',
    ])
    ->notName([
        '*.blade.php',
        '_ide_helper.php',
    ]);

$rules = [
    '@PSR12'                 => true,
    'array_syntax'           => ['syntax' => 'short'],
    'no_unused_imports'      => true,
    'single_quote'           => true,
    'no_extra_blank_lines'   => true,
    'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    'trim_array_spaces'      => true,
    'whitespace_after_comma_in_array' => true,
];

return (new PhpCsFixer\Config())
    ->setRules($rules)
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
