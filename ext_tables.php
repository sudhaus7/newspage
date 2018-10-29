<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
if (TYPO3_MODE == 'BE') {
    $languageFilePrefix = 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'SUDHAUS7\\Sudhaus7Newspage\\Hooks\\Backend\\Datamap';
    
    
    
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'SUDHAUS7\\Sudhaus7Newspage\\Hooks\\Backend\\Datamap';


    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\SUDHAUS7\Sudhaus7Newspage\Hooks\Backend\BackendEvaluator::class] = '';

    $extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY));

    $pluginSignature = $extensionName . '_plugin' ;
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        mod.wizards.newContentElement.wizardItems {
            special {
                elements {
                    '.$pluginSignature.' {
                        title = '.$languageFilePrefix.'tt_content.'.$pluginSignature.'
                        description = '.$languageFilePrefix.'tt_content.'.$pluginSignature.'.description
                        iconIdentifier = newspage-plugin
                        tt_content_defValues {
                            CType = list
                            list_type = '.$pluginSignature.'
                           
                        }
                    }
                }
                show := addToList('.$pluginSignature.')
            }
        }
    ');



    $pluginSignature = $extensionName . '_element' ;
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
        mod.wizards.newContentElement.wizardItems {
            common {
                elements {
                    '.$pluginSignature.' {
                        title = '.$languageFilePrefix.'tt_content.'.$pluginSignature.'
                        description = '.$languageFilePrefix.'tt_content.'.$pluginSignature.'.description
                        iconIdentifier = newspage-plugin
                        tt_content_defValues {
                            CType = '.$pluginSignature.'
                        }
                    }
                }
                show := addToList('.$pluginSignature.')
            }
        }
    ');
}
call_user_func(
    function ($extKey) {
        $confArr =
            unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sudhaus7newspage']);
        if ($confArr['sudhaus7newspagetype']) {
            $newsPageDoktype = 101;
            $GLOBALS['PAGES_TYPES'][$newsPageDoktype] = [
                'type' => 'web',
                'allowedTables' => '',
                'onlyAllowedTables' => true
            ];
            \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class)
                ->registerIcon(
                    'content-news',
                    TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
                    [
                        'source' => 'EXT:' . $extKey . '/Resources/Public/Icons/tx_sudhaus7newspage_domain_model_tag.png',
                    ]
                );

            // Allow backend users to drag and drop the new page type:
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
                'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $newsPageDoktype . ')'
            );
        }
    },
    'sudhaus7newspage'
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
TCEFORM.tt_content.tx_sudhaus7newspage_showimageindetail.disabled = 1
');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/Typoscript', 'Sudhaus7 NewsPage Templates');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_sudhaus7newspage_domain_model_tag');
