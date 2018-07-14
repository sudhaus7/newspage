<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
return array(

    'ctrl' => array(
        'title'             => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag',
        'label'             => 'title',
        'tstamp'            => 'tstamp',
        'crdate'            => 'crdate',
        'cruser_id'         => 'cruser_id',
        'dividers2tabs'     => true,
        'delete'            => 'deleted',
        'shadowColumnsForNewPlaceholders' => 'sys_language_uid,l10n_parent',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'languageField' => 'sys_language_uid',
        'enablecolumns'     => array(
            'disabled' => 'hidden',
        ),
        'searchFields'      => 'title,',
        'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sudhaus7_newspage') . 'Configuration/TCA/tx_sudhaus7newspage_domain_model_tag.php',
        'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('sudhaus7_newspage') . 'Resources/Public/Icons/tx_sudhaus7newspage_domain_model_tag.png'
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden, title',
    ),
    'types' => array(
        '1' => array('showitem' => '--palette--;Anzeige;1,title,parent_tag,--div--;Geoinformationen,map,geodata,georatio,countrydesc,churchdesc,staffdesc,--div--;Relationen,relation'),
    ),
    'palettes' => array(
        '1' => array('showitem' => 'hidden,sys_language_uid,t3ver_label,l10n_parent'),
    ),
    'columns' => array(
        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => array(
                    array(
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ),
                ),
                'default' => 0,
            )
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_sudhaus7newspage_domain_model_tag',
                'foreign_table_where' => 'AND tx_sudhaus7newspage_domain_model_tag.pid=###CURRENT_PID### AND tx_sudhaus7newspage_domain_model_tag.sys_language_uid IN (-1,0)',
            )
        ),
        'l10n_diffsource' => array(
            'config'=>array(
                'type'=>'passthrough')
        ),
        't3ver_label' => array(
            'displayCond' => 'FIELD:t3ver_label:REQ:true',
            'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
            'config' => array(
                'type'=>'none',
                'cols' => 27
            )
        ),
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'title' => array(
            'exclude' => 0,
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.title',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'category' => array(
            'exclude' => 0,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.category',
            'config' => array(
                'type' => 'select',
                'renderType'=>'selectSingle',
                'items' => array(
                    array('LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.category.country', 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.category.country'),
                    array('LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.category.topic', 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.category.topic'),
                )
            ),
        ),
        'relation'=>array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label'   => 'Inhalte',
            'config'  => array(
                'type'          => 'group',
                'internal_type' => 'db',
                'foreign_table' => 'tt_content',
                'allowed'       => 'tt_content',
                'MM'            => 'tx_sudhaus7newspage_domain_tag_mm',
                'MM_opposite_field' => 'tx_sudhaus7newspage_tag',
                'size'          => 10,
                'maxitems'      => 99999,
            ),
        ),

        'parent_tag' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.parent_tag',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'tx_sudhaus7newspage_domain_model_tag',
                'foreign_table_where' => 'and tx_sudhaus7newspage_domain_model_tag.sys_language_uid=0 ORDER BY tx_sudhaus7newspage_domain_model_tag.title ASC',
                'size' => 10,
                'autoSizeMax' => 50,
                'minitems' => 0,
                'maxitems' => 1,
                'renderType' => 'selectTree',
                'treeConfig' => array(
                    'expandAll' => true,
                    'parentField' => 'parent_tag',
                    'appearance' => array(
                        'showHeader' => true,
                        'width' => 400
                    ),
                )
            )
        ),

        'geodata' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.geodata',
            'config' => array(
                'type' => 'input',
                'size' => 30,
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
            ),
        ),
        'georatio' => array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.georatio',
            'config' => array(
                'type' => 'input',
                'size' => 5,
                'eval' => 'trim,num'
            ),
        ),

        'map'=> array(
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.map',
            'config'=>\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('map', array(
                'maxitems' => 1,
            ))
        ),

        'countrydesc' => array(
            'exclude' => 1,
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.countrydesc',
            'config' => array(
                'type' => 'text',
                'cols' => '80',
                'rows' => '40',
                'softref' => 'typolink_tag,images,email[subst],url',
                'wizards' => array(
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module' => array(
                            'name' => 'wizard_rte'
                        )
                    )
                )
            ),
        ),
        'churchdesc' => array(
            'exclude' => 1,
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.churchdesc',
            'config' => array(
                'type' => 'text',
                'cols' => '80',
                'rows' => '40',
                'softref' => 'typolink_tag,images,email[subst],url',
                'wizards' => array(
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module' => array(
                            'name' => 'wizard_rte'
                        )
                    )
                )
            ),
        ),
        'staffdesc' => array(
            'exclude' => 1,
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tx_sudhaus7newspage_domain_model_tag.staffdesc',
            'config' => array(
                'type' => 'text',
                'cols' => '80',
                'rows' => '40',
                'softref' => 'typolink_tag,images,email[subst],url',
                'wizards' => array(
                    'RTE' => array(
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
                        'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
                        'module' => array(
                            'name' => 'wizard_rte'
                        )
                    )
                )
            ),
        ),

    ),
);
