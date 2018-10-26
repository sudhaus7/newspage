<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin('SUDHAUS7.'.$_EXTKEY, 'Plugin', array('Plugin' => 'list,latest,rss,random' ), array('Plugin' => 'list,latest,rss,random' ), \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN);



\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SUDHAUS7.'.$_EXTKEY,
    'Element',
    array('Element' => 'show'),
    array(),
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['cliKeys']['migratettnews'] = array(
    'EXT:newspage/Classes/Cli/Migratettnews.php',
    '_CLI_lowlevel'
);


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][$_EXTKEY] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Hooks/Backend/PreviewView.php:SUDHAUS7\Newspage\Hooks\Backend\PreviewView';



$defaultConfig = include(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . '/Configuration/DefaultConfiguration.php');
$GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($defaultConfig, $GLOBALS['TYPO3_CONF_VARS']);


$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Imaging\IconRegistry::class
);
$iconRegistry->registerIcon(
    'newspage-plugin', // Icon-Identifier
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:newspage/Resources/Public/Icons/newspage.gif']
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['newspagelegacy']
    = \SUDHAUS7\Newspage\Updates\MigrateFromSudhaus7Newspage::class;
