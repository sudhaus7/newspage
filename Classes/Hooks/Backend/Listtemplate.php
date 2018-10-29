<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 23/09/2016
 * Time: 14:02
 */

namespace SUDHAUS7\Sudhaus7Newspage\Hooks\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class Listtemplate
{
    public function addFields(&$params)
    {
        $config = BackendUtility::getPagesTSconfig($params['flexParentDatabaseRow']['pid']);
        if (isset($config['mod.']['tx_sudhaus7newspage.']) && isset($config['mod.']['tx_sudhaus7newspage.']['listTemplates.']) && !empty($config['mod.']['tx_sudhaus7newspage.']['listTemplates.'])) {
            foreach ($config['mod.']['tx_sudhaus7newspage.']['listTemplates.'] as $k => $v) {
                $params['items'][] = [$v,$k];
            }
        }
    }
}
