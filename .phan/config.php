<?php

return [
    'target_php_version' => '7.3',
    'directory_list' => [
        'src'
    ],
    'exclude_file_regex' => '@^vendor/.*/(tests?|Tests?)/@',
    'exclude_analysis_directory_list' => [
        'vendor/'
    ]
];
