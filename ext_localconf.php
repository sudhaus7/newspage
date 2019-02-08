<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SUDHAUS7.'.$_EXTKEY,
    'Plugin',
    array('Plugin' => 'list,latest,rss,random' ),
    array('Plugin' => 'list,latest,rss,random' ),
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN
);

/*

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'SUDHAUS7.'.$_EXTKEY,
    'Element',
    array('Element' => 'show'),
    array(),
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

*/
/*
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['cliKeys']['migratettnews'] = array(
    'EXT:sudhaus7_newspage/Classes/Cli/Migratettnews.php',
    '_CLI_lowlevel'
);
*/




$defaultConfig = include(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . '/Configuration/DefaultConfiguration.php');
$GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($defaultConfig, $GLOBALS['TYPO3_CONF_VARS']);


$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Imaging\IconRegistry::class
);
$iconRegistry->registerIcon(
    'sudhaus7newspage-plugin', // Icon-Identifier
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:sudhaus7_newspage/Resources/Public/Icons/newspage.gif']
);
