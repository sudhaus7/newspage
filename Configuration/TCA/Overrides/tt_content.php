<?php
call_user_func(function () {
    global $TCA;
    $extKey = 'sudhaus7_newspage';
    
    $languageFilePrefix = 'LLL:EXT:'.$extKey.'/Resources/Private/Language/locallang.xlf:';
    $frontendLanguageFilePrefix = 'LLL:EXT:'.'frontend/Resources/Private/Language/locallang_ttc.xlf:';

    $confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sudhaus7_newspage']);
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sudhaus7_geomap')) {
        $confArr['newspagelatlngsupport']  = isset($confArr['newspagelatlngsupport']) ? $confArr['newspagelatlngsupport'] : false;
        $confArr['newspagelatlngactivate'] = isset($confArr['newspagelatlngactivate']) ? \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(
            ',',
            $confArr['newspagelatlngactivate'],
            true
        ) : [];
    } else {
        $confArr['newspagelatlngsupport']  = false;
        $confArr['newspagelatlngactivate'] = [];
    }

    // new tt_content columns
    $tempColumns = array(
        'tx_sudhaus7newspage_showimageindetail'=>array(
            //    'l10n_mode'=>'exclude',
            'exclude'=>0,
            'label'=>$languageFilePrefix.'element.flex.showimageindetail',
            'config'=>array(
                'type'=>'check',
                'default'=>1,
            )
        ),
        'tx_sudhaus7newspage_showdate'=>array(
            'l10n_mode'=>'exclude',
            'exclude'=>1,
            'label'=>$languageFilePrefix.'element.flex.showdate',
            'config'=>array(
                'type'=>'check',

            )
        ),
        'tx_sudhaus7newspage_showtime'=>array(
            'l10n_mode'=>'exclude',
            'exclude'=>1,
            'label'=>$languageFilePrefix.'element.flex.showtime',
            'config'=>array(
                'type'=>'check',
            )
        ),
        'tx_sudhaus7newspage_from'=>array(
            'exclude'=>0,
            'label'=>$languageFilePrefix.'element.flex.news.from',
            'config'=>array(
                'type' => 'input',
                'size' => 30,
                'eval'=>'datetime,required,'.\SUDHAUS7\Sudhaus7Newspage\Hooks\Backend\BackendEvaluator::class,
            )
        ),
        
        'tx_sudhaus7newspage_tag'=>array(
            'label'=>$languageFilePrefix.'element.flex.news.tags',
            'config'=>array(
                'type'=>'select',
                'renderType'=>'selectTree',
                'treeConfig'=>array(
                    'parentField'=>'parent_tag',
                    'appearance' => array(
                        'showHeader'=>true,
                        //'nonSelectableLevels'=>'0,1',
                    ),
                ),
                'foreign_table'=>'tx_sudhaus7newspage_domain_model_tag',
                'foreign_table_where'=>'AND tx_sudhaus7newspage_domain_model_tag.sys_language_uid=0 ORDER  by tx_sudhaus7newspage_domain_model_tag.title asc',
                'MM'=>'tx_sudhaus7newspage_domain_tag_mm',
                'size'=>10,
                'maxitems'=>999,
            )
        ),
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content", $tempColumns);


    $extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));

    // register newspage_element as content element
    $pluginSignature = $extensionName . '_element';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
        'tt_content',
        'CType',
        [
            $languageFilePrefix . 'tt_content.'.$pluginSignature,
            $pluginSignature,
            'content-text'
        ],
        'textmedia',
        'after'
    );

  $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$pluginSignature] = 'mimetypes-x-content-text';
    
    $TCA['tt_content']['palettes'][$extKey.'_datetime']['showitem'] = 'tx_sudhaus7newspage_from,tx_sudhaus7newspage_showdate';
    
    $GLOBALS['TCA']['tt_content']['types'][$pluginSignature] = [
        'showitem' => '
				--palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,
				--palette--;' . $frontendLanguageFilePrefix . 'palette.headers;headers,rowDescription,
				--palette--;;'.$extKey.'_datetime,tx_sudhaus7newspage_tag,
				bodytext;' . $languageFilePrefix . 'bodytext_formlabel,
				image,
				tx_sudhaus7newspage_showimageindetail,
			--div--;' . $frontendLanguageFilePrefix . 'tabs.appearance,
				layout;' . $frontendLanguageFilePrefix . 'layout_formlabel,
				--palette--;' . $frontendLanguageFilePrefix . 'palette.appearanceLinks;appearanceLinks,
				--palette--;' . $frontendLanguageFilePrefix . 'palette.mediaAdjustments;mediaAdjustments,
			--div--;' . $frontendLanguageFilePrefix . 'tabs.access,
				hidden;' . $frontendLanguageFilePrefix . 'field.default.hidden,
				--palette--;' . $frontendLanguageFilePrefix . 'palette.access;access,
			--div--;' . $frontendLanguageFilePrefix . 'tabs.extended,
            --div--;LLL:EXT:gridelements/Resources/Private/Language/locallang_db.xlf:gridElements,tx_gridelements_container,tx_gridelements_columns

		'
    ];
    // end write element to tt_content palette
   
    
    $pluginSignature = $extensionName . '_plugin' ;
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('SUDHAUS7.'.$extKey, 'Plugin', $languageFilePrefix.'tt_content.'.$pluginSignature);
    $TCA['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';
    $TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $extKey . '/Configuration/FlexForms/Plugin.xml');


    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$extKey] = \SUDHAUS7\Sudhaus7Newspage\Hooks\Backend\PreviewView::class;
});
