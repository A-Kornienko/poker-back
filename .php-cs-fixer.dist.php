<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRules([
        'no_unused_imports' => true,
        '@PSR12'                                   => true,
        'align_multiline_comment'                  => true,
        'array_indentation'                        => true,
        'array_push'                               => true,
        'assign_null_coalescing_to_coalesce_equal' => true,
        'binary_operator_spaces'                   => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '='  => 'align_single_space_minimal',
                '|'  => 'no_space',
                '&'  => null
            ]
        ],
        'blank_line_before_statement' => [
            'statements' => [
                'yield',
                'yield_from',
                'throw',
                'try',
                'return'
            ]
        ],
        'class_attributes_separation'             => ['elements' => ['method' => 'one', 'property' => 'one']],
        'combine_consecutive_issets'              => true,
        'combine_consecutive_unsets'              => true,
        'concat_space'                            => ['spacing' => 'one'],
        'control_structure_continuation_position' => true,
        'dir_constant'                            => true,
        'fopen_flag_order'                        => true,
        'function_declaration'                    => ['closure_fn_spacing' => 'none'],
        'general_phpdoc_annotation_remove'        => ['annotations' => ['author']],
        // 'global_namespace_import'                 => true,
        'list_syntax'                             => true,
        'logical_operators'                       => true,
        'mb_str_functions'                        => true,
        'method_argument_space'                   => ['on_multiline' => 'ensure_fully_multiline'],
        'method_chaining_indentation'             => true,
        'modernize_types_casting'                 => true,
        'multiline_comment_opening_closing'       => true,
        'multiline_whitespace_before_semicolons'  => true,
        'new_with_braces'                         => false,
        'no_alias_functions'                      => true,
        'no_extra_blank_lines'                    => [
            'tokens' => [
                'break',
                'case',
                'continue',
                'curly_brace_block',
                'default',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'switch',
                'throw',
                'use',
                'return',
            ]
        ],
        'no_homoglyph_names'         => true,
        'no_superfluous_phpdoc_tags' => false,
        'no_useless_else'            => true,
        'no_useless_return'          => true,
        'operator_linebreak'         => true,
        'ordered_class_elements'     => [
            'order' => [
                'use_trait',

                'constant_public',
                'constant_protected',
                'constant_private',

                'property_public_static',
                'property_protected_static',
                'property_private_static',

                'property_public',
                'property_protected',
                'property_private',

                'method_public_abstract',
                'method_protected_abstract',

                'method_public_abstract_static',
                'method_protected_abstract_static',

                'construct',

                'method_public',
                'method_protected',
                'method_private',

                'method_public_static',
                'method_protected_static',
                'method_private_static',

                'destruct',
                'magic',
                'phpunit'
            ]
        ],
        'ordered_imports' => [
            'imports_order'  => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha'
        ],
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
        'phpdoc_line_span'                    => true,
        'phpdoc_order'                        => true,
        'phpdoc_summary'                      => false,
        'return_assignment'                   => true,
        'simplified_if_return'                => true,
        'single_trait_insert_per_statement'   => false,
        'ternary_to_null_coalescing'          => true,
        'trailing_comma_in_multiline'         => false,
        'use_arrow_functions'                 => true,
        'yoda_style'                          => false
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
