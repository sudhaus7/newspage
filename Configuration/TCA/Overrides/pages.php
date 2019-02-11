<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function ($extKey, $table) {
        $languageFilePrefix = 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:';
        $confArr =
            unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sudhaus7_newspage']);
        if ($confArr['newspagetype']) {

            $newsPageDoktype = 101;
            // Add new page type as possible select item:
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
                $table,
                'doktype',
                [
                    'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:news_page_type',
                    $newsPageDoktype,
                    'EXT:' . $extKey . 'Resources/Public/Images/tx_sudhaus7newspage_domain_model_tag.png'
                ],
                '1',
                'after'
            );
            // Add icon for new page type:
            \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule(
                $GLOBALS['TCA']['pages'],
                [
                    'ctrl' => [
                        'typeicon_classes' => [
                            $newsPageDoktype => 'content-news',
                        ],
                    ],
                ]
            );

            $GLOBALS['TCA']['pages']['palettes']['newspage'] = [
                'showitem' => 'doktype;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.doktype_formlabel,--linebreak--,content_from_pid',

            ];
            $GLOBALS['TCA']['pages']['types'][$newsPageDoktype] = [
                'showitem' =>'
                --palette--;'.$languageFilePrefix.'.news_page_type;newspage,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.title;title,
                --palette--;LLL:EXT:realurl/Resources/Private/Language/locallang_db.xlf:pages.palette_title;tx_realurl,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility;visibility,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.access;access,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.metadata,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.abstract;abstract,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.editorial;editorial,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.appearance,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.layout;layout,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.behaviour,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.links;links,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.language;language,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.miscellaneous;miscellaneous,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.resources,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.media;media,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.config;config,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,
                --div--;LLL:EXT:lang/locallang_tca.xlf:sys_category.tabs.category,categories',
                'columnsOverrides' => [
                    'content_from_pid' => [
                        'config' => [
                            'type' => 'user',
                            'userFunc' => SUDHAUS7\Sudhaus7Newspage\Hooks\Backend\Tca::class.'->tx_sudhaus7newspage_select'
                        ],
                        'wizards' => []
                    ]
                ]
            ];
        }
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
            $extKey,
            'Configuration/PageTSconfig/page.tsconfig',
            $languageFilePrefix.'pages.tsconfig.title'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('@import \'EXT:sudhaus7_newspage/Configuration/PageTSconfig/page.tsconfig\'');
    },
    'sudhaus7_newspage',
    'pages'
);
