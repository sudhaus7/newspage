<?php
namespace SUDHAUS7\Sudhaus7Newspage\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TagRepository
 *
 * @package SUDHAUS7\Sudhaus7Newspage\Domain\Repository
 */
class TagRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    protected $defaultOrderings = array("title" => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING);

    public function findAllList($list, $pages=[])
    {
        if (!is_array($list)) {
            $list = GeneralUtility::trimExplode(',', $list, true);
        }
        if (empty($list)) {
            return $this->findAll();
        }
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        if (!empty($pages)) {
            $querySettings->setRespectStoragePage(false);
            $querySettings->setStoragePageIds($pages);
        }
        $query->setQuerySettings($querySettings);
        $constraints = [];
        $tags = [];
        foreach ($list as $id) {
            $tags[] = $query->equals('uid', (int)$id);
        }
        $constraints[] = $query->logicalOr($tags);
        $query->matching($query->logicalAnd($constraints));
        return $query->execute();
    }

    public function findRelated(\SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent $o)
    {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Query $query */
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $query->setQuerySettings($querySettings);

        $query->matching($query->contains('related', $o));

        return $query->execute();
    }


    public function findByParent($parent=null)
    {
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(false);
        $query->setQuerySettings($querySettings);

        if ($parent) {
            $query->matching($query->equals('parent_tag', $parent));
        }

        return $query->execute();
    }

    private function enableFields($tableName)
    {
        if (TYPO3_MODE === 'FE') {
            // Use enableFields in frontend mode
            $enableFields = $GLOBALS['TSFE']->sys_page->enableFields($tableName);
        } else {
            // Use enableFields in backend mode
            $enableFields = \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause($tableName);
            $enableFields .= \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields($tableName);
        }
        return $enableFields;
    }
}
