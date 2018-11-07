<?php

namespace SUDHAUS7\Sudhaus7Newspage\Domain\Repository;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\QueryGenerator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Class TtContentRepository
 *
 * @package SUDHAUS7\Sudhaus7Newspage\Domain\Repository
 */
class TtContentRepository extends Repository
{


    /**
     * @param array $pages
     * @param array $settings
     *
     * @return mixed
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function findNews(array $pages, array $settings)
    {

        //\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::getTreeList()
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(true);
        $querySettings->setIncludeDeleted(false);
        $querySettings->setIgnoreEnableFields(false);
        $querySettings->setLanguageUid($GLOBALS['TSFE']->sys_language_uid);

        $querySettings->setStoragePageIds($pages);
        $query->setQuerySettings($querySettings);
        $order = $settings['sortby']=='desc' ? QueryInterface::ORDER_DESCENDING : QueryInterface::ORDER_ASCENDING;

        $query->setOrderings(array('tx_sudhaus7newspage_from'=>$order));


        $this->addStandardConstraints($query, $settings);


        if (!isset($settings['page'])) {
            $settings['page'] = 0;
        }
        if (!isset($settings['max'])) {
            $settings['max'] = 9999999;
        }

        //  if (!isset($settings['highlights']) || empty($settings['highlights'])) {
        $query->setOffset((int)$settings['page']);
        $query->setLimit((int)$settings['max']);
        //  }

        $this->findNewsPreDb_dispatch($query, $settings, $pages);
        return $query->execute();
    }

    /**
     * @param $query
     * @param $settings
     *
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    private function findNewsPreDb_dispatch(&$query, $settings, $pages)
    {
        /** @var Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        $data = ['query'=>$query,'settings'=>$settings,'pages'=>$pages];
        $ret = $signalSlotDispatcher->dispatch(__CLASS__, 'findNewsPreDb', [$data]);
        $query = $ret[0]['query'];
    }

    /**
     * @param QueryInterface $query
     * @param array $settings
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    protected function addStandardConstraints(QueryInterface &$query, array $settings)
    {
        $constraints = array();
        
        // TO BE REMOVED
        //if (!isset($settings['displaytype']) || empty($settings['displaytype'])) {
        //    $settings['displaytype'] = 1;
        //}
        //$constraints[] = $query->equals('tx_sudhaus7newspage_type', $settings['displaytype']);
        
        
        $constraints[] = $query->equals('ctype', 'sudhaus7newspage_element');



        if (isset($settings['tags']) && !empty($settings['tags'])) {
            $list = GeneralUtility::trimExplode(',', $settings['tags'], true);
            $tags = array();
            foreach ($list as $id) {
                $tags[] = $query->contains('txSudhaus7newspageTag', (int)$id);
            }
            $constraints[] = $query->logicalOr($tags);
        }


        if (isset($settings['highlights']) && !empty($settings['highlights'])) {
            $constraints[]=$query->equals('tx_sudhaus7newspage_highlight', 1);
        }


        if (isset($settings['month']) && !empty($settings['month'])) {
            $from = new \DateTime($settings['month'].'-01 00:00:00');
            $to = new \DateTime($from->format('Y-m-t').' 23:59:59');

            $constraints[]=$query->greaterThanOrEqual('tx_sudhaus7newspage_from', $from);
            $constraints[]=$query->lessThanOrEqual('tx_sudhaus7newspage_from', $to);
        }


        if (isset($settings['scope']) && !empty($settings['scope']) && $settings['scope'] > 0) {
            if (!isset($settings['datetimeStringForFilteringNews']) || empty($settings['datetimeStringForFilteringNews'])) {
                $settings['datetimeStringForFilteringNews']='now';
            }
            $now = new \DateTime($settings['datetimeStringForFilteringNews']);
            $from = new \DateTime($now->format('Y-m-d').' 00:00:00');
            if ($settings['scope']==1) {
                $constraints[]=$query->lessThanOrEqual('tx_sudhaus7newspage_from', $from);
            }
            if ($settings['scope']==2) {
                $constraints[]=$query->greaterThanOrEqual('tx_sudhaus7newspage_from', $from);
            }
        }



        if (isset($settings['debug']) && !empty($settings['debug'])) {
            $constraints[]=$query->equals('a', 'x');
        }
        $constraints[]=$query->equals('deleted', '0');

        $query->matching($query->logicalAnd($constraints));
        //return $query;
    }

    public function findNextContent(\SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent $news)
    {
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(true);
        $querySettings->setLanguageUid($GLOBALS['TSFE']->sys_language_uid);

        $querySettings->setStoragePageIds([$news->getPid()]);
        $query->setQuerySettings($querySettings);
        $query->setOrderings(array('sorting' => QueryInterface::ORDER_ASCENDING));

        $constraints = [];

        $constraints[] = $query->equals('ctype', 'text');
        $constraints[] = $query->equals('ctype', 'textmedia');
        $constraints[] = $query->equals('ctype', 'bildtextteaser');
        $query->matching($query->logicalOr($constraints));

        $query->setOffset(0);
        $query->setLimit(1);
        return $query->execute();
    }

    /**
     * @param array $pages
     * @param array $settings
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findNewsMax(array $pages, array $settings)
    {

        //\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::getTreeList()
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(true);
        $querySettings->setStoragePageIds($pages);
        $querySettings->setLanguageUid($GLOBALS['TSFE']->sys_language_uid);
        $query->setQuerySettings($querySettings);
        $this->addStandardConstraints($query, $settings);


        return $query->count();
    }


    /**
     * @param $pages
     * @param \SUDHAUS7\Sudhaus7Newspage\Domain\Model\Tag $tag
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByTag($pages, $tag)
    {
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(true);
        $querySettings->setStoragePageIds($pages);
        $querySettings->setLanguageUid($GLOBALS['TSFE']->sys_language_uid);
        $query->setQuerySettings($querySettings);
        $query->matching(
            $query->contains('txSudhaus7newspageTag', $tag)
        );
        return $query->execute();
    }

    /**
     * @param $pages
     * @param \SUDHAUS7\Sudhaus7Newspage\Domain\Model\Tag[] $tags
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByMultipleTags($pages, $tags)
    {
        $query = $this->createQuery();
        $querySettings = $query->getQuerySettings();
        $querySettings->setRespectStoragePage(true);
        $querySettings->setStoragePageIds($pages);
        $querySettings->setLanguageUid($GLOBALS['TSFE']->sys_language_uid);
        $query->setQuerySettings($querySettings);
        $constraints = [];
        foreach ($tags as $tag) {
            $constraints[]= $query->contains('txSudhaus7newspageTag', $tag);
        }
        $query->matching($query->logicalAnd($constraints));
        return $query->execute();
    }

    public function getTreeList($id, $depth)
    {
        $query = new QueryGenerator();
        $aList = [];
        $ids = GeneralUtility::trimExplode(',', $id, true);
        foreach ($ids as $checkid) {
            \array_push($aList, $checkid);
            $list = $query->getTreeList($checkid, $depth, 0, '1=1'.BackendUtility::BEenableFields('pages'));
            $aList = \array_merge($aList, GeneralUtility::trimExplode(',', $list, true));
        }

        return \array_unique($aList);
    }
}
