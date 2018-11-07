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

    $TCA['tt_content']['ctrl']['requestUpdate'] .= ',tx_sudhaus7newspage_type';
    $tempColumns = array(

        'tx_sudhaus7newspage_type'=>array(

            'exclude'=>0,
            'label'=>$languageFilePrefix.'element.flex.type',
            'config'=>array(
                'type'=>'select',
                'renderType'=>'selectSingle',
                'size'=>1,
                'items'=>array(
                    array($languageFilePrefix.'element.flex.type.pleaseselect',0),
                    array($languageFilePrefix.'element.flex.type.news',1),
                    array($languageFilePrefix.'element.flex.type.event',2),
                    array($languageFilePrefix.'element.flex.type.project',3),
                ),
            )
        ),

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
            //    'l10n_mode'=>'exclude',

            'displayCond' => array(
                'OR'=> array(
                    'FIELD:tx_sudhaus7newspage_type:=:1',
                    'FIELD:tx_sudhaus7newspage_type:=:2',
                ),
            ),
            'exclude'=>1,
            'label'=>$languageFilePrefix.'element.flex.showdate',
            'config'=>array(
                'type'=>'check',

            )
        ),
        'tx_sudhaus7newspage_showtime'=>array(
            'displayCond' => 'FIELD:tx_sudhaus7newspage_type:=:2',
            'l10n_mode'=>'exclude',

            'exclude'=>0,
            'label'=>$languageFilePrefix.'element.flex.showtime',
            'config'=>array(
                'type'=>'check',

            )
        ),
        'tx_sudhaus7newspage_from'=>array(
            'displayCond' => array(
                'OR'=> array(
                    'FIELD:tx_sudhaus7newspage_type:=:1',
                    'FIELD:tx_sudhaus7newspage_type:=:2',
                ),
            ),
            'exclude'=>0,
            'label'=>$languageFilePrefix.'element.flex.news.from',
            'config'=>array(
                'type' => 'input',
                'size' => 30,
                'eval'=>'datetime,required,'.\SUDHAUS7\Sudhaus7Newspage\Hooks\Backend\BackendEvaluator::class,
            )
        ),
        'tx_sudhaus7newspage_to'=>array(

            'displayCond' => 'FIELD:tx_sudhaus7newspage_type:=:2',
            'exclude'=>0,
            'label'=>$languageFilePrefix.'element.flex.news.to',
            'config'=>array(
                'type' => 'input',
                'size' => 30,
                'eval'=>'datetime',
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
        'tx_sudhaus7newspage_latlng'=>array(
            /*
            'displayCond' => array(
                'OR'=> array(
                    'FIELD:tx_sudhaus7newspage_type:=:1',
                    'FIELD:tx_sudhaus7newspage_type:=:3',
                ),
            ),
            */
            'exclude'=>1,
            'label'=>$languageFilePrefix.'element.flex.news.latlng',
            'l10n_mode'=>'exclude',
            'config'=>array(
                'type'=>'input',
                'size' => '80',
                'eval' => 'trim',
                "wizards" => array(
                    "_PADDING" => 2,
                    "link" => array(
                        "type" => "popup",
                        "title" => "Google Map GEO Selector",
                        "icon" => "EXT:sudhaus7_geomap/Resources/Public/Icons/geo_popup.gif",
                        'module' => array(
                            'name' => 'wizard_sudhaus7geolocationpicker',
                        ),
                        "JSopenParams" => "height=350,width=500,status=0,menubar=0,scrollbars=1"
                    ),
                ),
            )
        ),
        'tx_sudhaus7newspage_who'=>array(
            'displayCond' => 'FIELD:tx_sudhaus7newspage_type:=:2',
            'exclude' => 1,
            'label'=>$languageFilePrefix.'element.flex.who',
            'config'=>array(
                'type' => 'input',
                'size' => 30,
            )
        ),
        'tx_sudhaus7newspage_place'=>array(
            'displayCond' => 'FIELD:tx_sudhaus7newspage_type:=:2',
            'exclude' => 1,
            'label'=>$languageFilePrefix.'element.flex.place',
            'config'=>array(
                'type' => 'input',
                'size' => 30,
            )
        ),
    );

    /*
            'displayCond' => array(
                'OR'=> array(
                    'FIELD:tx_sudhaus7newspage_type:=:1',
                    'FIELD:tx_sudhaus7newspage_type:=:3',
                ),
            ),
            */

    if (!empty($confArr['newspagelatlngactivate'])) {
        $tempColumns['tx_sudhaus7newspage_latlng']['displayCond'] = ['OR'=>[]];
        foreach ($confArr['newspagelatlngactivate'] as $v) {
            array_push($tempColumns['tx_sudhaus7newspage_latlng']['displayCond']['OR'], sprintf('FIELD:tx_sudhaus7newspage_type:=:%d', $v));
        }
        
        // if there is just one entry, add a dummy entry for the OR condition to work
        if (sizeof($tempColumns['tx_sudhaus7newspage_latlng']['displayCond']['OR']) == 1) {
            array_push($tempColumns['tx_sudhaus7newspage_latlng']['displayCond']['OR'], sprintf('FIELD:tx_sudhaus7newspage_type:=:%d', 99999));
        }
        
        // if no items have been added, delete the setting
        if (empty($tempColumns['tx_sudhaus7newspage_latlng']['displayCond']['OR'])) {
            unset($tempColumns['tx_sudhaus7newspage_latlng']['displayCond']);
        }
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content", $tempColumns);


    $extensionName = strtolower(\TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($extKey));
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('SUDHAUS7.'.$extKey, 'Element', 'News Element');
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


    $TCA['tt_content']['palettes'][$extKey.'_datetime']['showitem'] = 'tx_sudhaus7newspage_from,tx_sudhaus7newspage_to,tx_sudhaus7newspage_showdate';

    //$tx_sudhaus7newspage_latlng = $confArr['newspagelatlngsupport']?'tx_sudhaus7newspage_latlng,':'';

    $GLOBALS['TCA']['tt_content']['types'][$pluginSignature] = [
        'showitem' => '
				--palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,tx_sudhaus7newspage_type,
				--palette--;' . $frontendLanguageFilePrefix . 'palette.headers;headers,tx_sudhaus7newspage_place,tx_sudhaus7newspage_who,rowDescription,
				--palette--;;'.$extKey.'_datetime,tx_sudhaus7newspage_tag,
				bodytext;' . $languageFilePrefix . 'bodytext_formlabel,image,tx_sudhaus7newspage_showimageindetail,
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
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    TCEFORM.tt_content.imagewidth.types.sudhaus7newspage_element.disabled = 1
    TCEFORM.tt_content.imageheight.types.sudhaus7newspage_element.disabled = 1
    TCEFORM.tt_content.imageborder.types.sudhaus7newspage_element.disabled = 1
    ');
    $pluginSignature = $extensionName . '_plugin' ;
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('SUDHAUS7.'.$extKey, 'Plugin', $languageFilePrefix.'tt_content.'.$pluginSignature);
    $TCA['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,pages,recursive';
    $TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $extKey . '/Configuration/FlexForms/Plugin.xml');

    // newspage calendar registration field
    /** @var \TYPO3\CMS\Core\Package\PackageManager $packageManager */
    $packageManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Package\PackageManager::class);
    if ($packageManager->isPackageActive('cal')) {
        $calColumns = [
            'tx_sudhaus7newspage_calendar' => [
                'displayCond' => 'FIELD:tx_sudhaus7newspage_type:=:2',
                'exclude' => 1,
                'label' => $languageFilePrefix.'element.flex.calendar',
                'config' => [
                    'type' => 'group',
                    'internal_type' => 'db',
                    'allowed' => 'tx_cal_calendar',
                    'foreign_table' => 'tx_cal_calendar',
                    'foreign_table_where' => ' AND tx_cal_calendar.type=0',
                    'maxitems' => 1,
                    'minitems' => 0,
                    'wizards' => [
                        'suggest' => [
                            'type' => 'suggest'
                        ]
                    ]
                ]
            ]
        ];
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns("tt_content", $calColumns);
    }
});
