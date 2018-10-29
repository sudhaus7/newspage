<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function ($extKey, $table) {
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
        }
    },
    'sudhaus7_newspage',
    'pages_language_overlay'
);
