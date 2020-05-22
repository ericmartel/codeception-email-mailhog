<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__);

return PhpCsFixer\Config::create()
    ->setRules([
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_align' => false,
        'phpdoc_to_comment' => false,
        'header_comment' => false,
    ])
    ->setFinder($finder);