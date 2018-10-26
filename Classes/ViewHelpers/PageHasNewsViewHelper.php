<?php
/**
 * Created by PhpStorm.
 * User: markus
 * Date: 26.10.18
 * Time: 10:27
 */

namespace SUDHAUS7\Newspage\ViewHelpers;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

class PageHasNewsViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('content','mixed','The content Element to check',false);
        $this->registerArgument('page', 'mixed', 'The page to check for', false);
    }

    /**
     * @param null $arguments
     * @return bool
     */
    protected static function evaluateCondition($arguments = null)
    {
        $dbConnection = GeneralUtility::makeInstance(ConnectionPool::class);
        $contentConnector = $dbConnection->getQueryBuilderForTable('tt_content');
        $hasNews = false;
        if (empty($arguments['content']) && empty($arguments['page'])) {
            throw new \InvalidArgumentException('Neither content nor page is set in '.self::class,1540542726);
        }

        if (
            (is_array($arguments['content']) || is_object($arguments['content']))
            &&
            (is_array($arguments['page']) || is_object($arguments['page']))
        ) {
            throw new \InvalidArgumentException('Given arguments in ' . self::class . ' are ambivalent', 1540543089);
        }

        if (isset($arguments['content'])) {
            $pageId = $arguments['content']['pid'];
            if (!$pageId) {
                try {
                    $pageId = $arguments['content']->getPid();
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException('Content is no array or object doesn\'t match', 1540543292);
                }
            }

            $newsElement = $contentConnector->select('*')->from('tt_content')
                ->where(
                    $contentConnector->expr()->eq('pid', $pageId),
                    $contentConnector->expr()->eq('deleted', 0),
                    $contentConnector->expr()->eq('hidden', 0),
                    $contentConnector->expr()->eq('CType', '"sudhaus7newspage_element"')
                )->execute()->fetchAll();
            if (count($newsElement)> 0) {
                $hasNews = true;
            }
        }

        if (isset($arguments['page'])) {
            $pageId = $arguments['page']['uid'];
            if (!$pageId) {
                try {
                    $pageId = $arguments['page']->getUid();
                } catch (\Exception $e) {
                    throw new \InvalidArgumentException('Page is no array or object doesn\'t match', 1540544571);
                }
            }

            $newsElement = $contentConnector->select('*')->from('tt_content')
                ->where(
                    $contentConnector->expr()->eq('pid', $pageId),
                    $contentConnector->expr()->eq('deleted', 0),
                    $contentConnector->expr()->eq('hidden', 0),
                    $contentConnector->expr()->eq('CType', '"sudhaus7newspage_element"')
                )->execute()->fetchAll();
            if (count($newsElement)> 0) {
                $hasNews = true;
            }
        }

        return $hasNews;
    }
}
