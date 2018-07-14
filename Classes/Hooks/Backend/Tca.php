<?php
/**
 * Created by PhpStorm.
 * User: markus
 * Date: 26.07.17
 * Time: 14:19
 */

namespace SUDHAUS7\Sudhaus7Newspage\Hooks\Backend;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class Tca
{
    public function tx_sudhaus7newspage_select($PA, $fObj)
    {
        $hookObjectsArr = [];

        /** @var QueryGenerator $queryGenerator */
        $queryGenerator = GeneralUtility::makeInstance(QueryGenerator::class);
        $query = '
        SELECT pages.* FROM pages
        WHERE pages.hidden=0
        AND pages.deleted=0
        AND pages.is_siteroot=1
        ORDER BY pages.sorting ASC
        ';
        $all =[];
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['SUDHAUS7\\Sudhaus7Newspage\\Hooks\\Backend\\Tca']['Hooks'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['SUDHAUS7\\Sudhaus7Newspage\\Hooks\\Backend\\Tca']['Hooks'] as $classRef) {
                $hookObject = GeneralUtility::getUserObj($classRef);
                if (method_exists($hookObject, 'alterRootQuery')) {
                    $query = $hookObject->alterRootQuery($query, $this);
                }
                $hookObjectsArr[] = $hookObject;
            }
        }
        $result = $this->getDb()->sql_query($query);
        $singleQuery ='
            SELECT pages.*, tt_content.tx_sudhaus7newspage_from FROM pages
            JOIN tt_content
            ON tt_content.pid=pages.uid
            AND tt_content.CType="sudhaus7newspage_element"
            AND tt_content.tx_sudhaus7newspage_type=1
            AND tt_content.hidden=0
            AND tt_content.deleted=0
            WHERE pages.hidden=0
            AND pages.deleted=0
            AND pages.uid in(%1$s)
            ORDER BY tt_content.tx_sudhaus7newspage_from DESC
            ';
        foreach ($hookObjectsArr as $hookObject) {
            if (method_exists($hookObject, 'alterSingleQuery')) {
                $singleQuery = $hookObject->alterSingleQuery($singleQuery, $this);
            }
        }
        while ($row = $result->fetch_assoc()) {
            $all[$row['uid']] = [
                'page' => $row
            ];
            $treeList = $queryGenerator->getTreeList($row['uid'], 10, 0, 1);

            $res = $this->getDb()->sql_query(sprintf($singleQuery, $treeList));
            while ($single = $res->fetch_assoc()) {
                $all[$row['uid']]['items'][] = $single;
            }
        }
        $formfield = '<div class="form-control-wrap">';
        $formfield .= '<select name="'.$PA['itemFormElName'].'" class="form-control form-control-adapt">';
        $formfield .= '<option value=""></option>';
        $actual = $this->evaluateActualValuePage($PA['itemFormElValue']);
        foreach ($all as $groups) {
            $formfield .= '<optgroup label="'.$groups['page']['title'].'">';
            if ($groups['items']) {
                foreach ($groups['items'] as $item) {
                    $formfield .= '<option value="'.$item['uid'].'" '.(($item['uid'] == $actual) ? 'selected' : '').'>'.date('[d.m.Y H:i] ', $item['tx_sudhaus7newspage_from']).$item['title'].'</option>';
                }
            }
            $formfield .= '</opgroup>';
        }
        $formfield .= '</select>';
        $formfield .= '</div>';
        return $formfield;
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDb()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @param $string
     * @return int
     */
    protected function evaluateActualValuePage($string)
    {
        $first = explode('|', $string);
        if (is_array($first) && count($first) == 2) {
            $second = explode('_', $first[0]);
            if (is_array($second) && count($second) == 2) {
                return (int)$second[1];
            }
        }
        return (int)$string;
    }
}
