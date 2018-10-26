<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 22/09/2016
 * Time: 17:51
 */

namespace SUDHAUS7\Newspage\Hooks\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;

class Latesttemplate
{
    public function addFields(&$params)
    {
        $config = BackendUtility::getPagesTSconfig($params['flexParentDatabaseRow']['pid']);
        if (isset($config['mod.']['tx_newspage.']) && isset($config['mod.']['tx_newspage.']['latestTemplates.']) && !empty($config['mod.']['tx_newspage.']['latestTemplates.'])) {
            foreach ($config['mod.']['tx_newspage.']['latestTemplates.'] as $k => $v) {
                $params['items'][] = [$v,$k];
            }
        }
    }
}
