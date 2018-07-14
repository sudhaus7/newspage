<?php


return [

    'SYS'=>[
        'caching'=>[
            'cacheConfigurations'=>[
                'sudhaus7newspage_pagecache'=>[
                    'backend'=>\TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class,
                    'frontend'=>\TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
                    'groups'=>['pages'],
                    'options'=>[
                        'defaultLifetime'=>0,
                    ]
                ],
            ],
        ]
    ]
];
