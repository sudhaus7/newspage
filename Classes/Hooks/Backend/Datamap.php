<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 04/12/15
 * Time: 16:04
 */

namespace SUDHAUS7\Sudhaus7Newspage\Hooks\Backend;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class Datamap
{
    /**
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @var PackageManager
     */
    protected $pm;

    /**
     *
     */
    public function __construct()
    {
        $this->databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tt_content');
        $this->cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $this->pm = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Package\\PackageManager');
    }

    /**
     * @param $status
     * @param $table
     * @param $id
     * @param $fieldArray
     * @param DataHandler $pObj
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, DataHandler &$pObj)
    {
        if ($status=='new' && $table=='tt_content' && $fieldArray['CType']=='sudhaus7newspage_element') {
            $uid = $id;
            if ((int)$uid!==$id) {
                $uid=$pObj->substNEWwithIDs[$id];
            }
            $this->handleTTcontent($uid, $fieldArray);
            if (isset($fieldArray['pid']) && (int)$fieldArray['pid'] > 0) {
                if ($this->pm->isPackageAvailable('realurl')) {
                    $this->deleteRealurlCache($fieldArray['pid']);
                }
                $this->cacheManager->flushCachesByTag('pageId_' . $fieldArray['pid']);
                $rootline = BackendUtility::BEgetRootLine($fieldArray['pid']);
                $rootid = $rootline[0]['uid'];
                foreach ($rootline as $p) {
                    if ($p['is_siteroot']) {
                        $rootid = $p['uid'];
                    }
                }
                $this->cacheManager->flushCachesByTag('sudhaus7newspage_element_root_'.$rootid);
            }
        }
    }

    public function processCmdmap($command, $table, $id, $value, $commandIsProcessed, DataHandler &$pObj, $pasteUpdate)
    {
        if ($command == 'move' && $table=='tt_content') {
            $row = BackendUtility::getRecord('tt_content', $id);
            if (!empty($row)) {
                if ($this->pm->isPackageAvailable('realurl')) {
                    $this->deleteRealurlCache($row['pid']);
                }
                $this->cacheManager->flushCachesByTag('pageId_' . $row['pid']);
            }
        }
    }

    public function processCmdmap_postProcess($command, $table, $id, $value, DataHandler &$pObj, $pasteUpdate, $pasteDatamap)
    {
        if ($command=='delete' && $table=='tt_content') {
            $row = BackendUtility::getRecord($table, $id);
            if (!empty($row) && $row['CType']=='sudhaus7newspage_element') {
                if ($this->pm->isPackageAvailable('realurl')) {
                    $this->deleteRealurlCache($row['pid']);
                }
                $this->cacheManager->flushCachesByTag('pageId_' . $row['pid']);
            }
        }
        if ($command == 'delete' && $table == 'pages') {
            $this->databaseConnection->update($table, ['hidden' => 1], ['uid'=>$id]);
        }
        if (($command=='move' || $command=='undelete' || $command=='copy') && $table=='tt_content') {
            $dummy = [];
            $this->handleTTcontent($id, $dummy);
        }
    }

    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, DataHandler &$pObj)
    {
        if ($status == 'update') {
            if ($table == 'pages') {
                if (isset($fieldArray['title'])) {
                    //Dies this page have newspage elements?
                    $query = $this->databaseConnection->createQueryBuilder();
                    $query->select(...['*'])->from('tt_content');
                    $query->andWhere($query->expr()->eq('CType', 'sudhaus7newspage_element'));
                    $query->andWhere($query->expr()->eq('deleted', 0));
                    $query->andWhere($query->expr()->eq('pid', $id));
                    $query->orderBy('hidden', 'ASC');
                    $result = $query->execute();
                    $row = $result->fetch(\PDO::FETCH_ASSOC);
                    if ($row) {
                        if ($this->pm->isPackageAvailable('realurl')) {
                            if ($row['tx_sudhaus7newspage_showdate'] > 0) {
                                $fieldArray['tx_realurl_pathsegment'] = date(
                                    'd-m-Y',
                                        $row['tx_sudhaus7newspage_from']
                                ) . '-' . $this->generateslug($fieldArray['title']);
                            } else {
                                $fieldArray['tx_realurl_pathsegment'] = '';
                            }
                            $this->deleteRealurlCache($id);
                        }
                        $this->cacheManager->flushCachesByTag('pageId_'.$row['pid']);
                    }
                }
                if (
                    (isset($fieldArray['hidden']) && $fieldArray['hidden'] == 1) ||
                    (isset($fieldArray['deleted']) && $fieldArray['deleted'] == 1)
                ) {
                    $this->databaseConnection->update('pages', ['hidden' => 1], ['content_from_pid'=>$id]);
                }
            }
            if ($table == 'pages_language_overlay') {
                $pages_language_overlay = BackendUtility::getRecord('pages_language_overlay', $id);
                
                if (isset($fieldArray['title'])) {
                    $query = $this->databaseConnection->createQueryBuilder();
                    $query->select(...['*'])->from('tt_content');
                    $query->andWhere($query->expr()->eq('CType', 'sudhaus7newspage_element'));
                    $query->andWhere($query->expr()->eq('deleted', 0));
                    $query->andWhere($query->expr()->eq('pid', $pages_language_overlay['pid']));
    
                    $query->orderBy('hidden', 'ASC');
                    $result = $query->execute();
                    $row = $result->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($row) {
                        if ($this->pm->isPackageAvailable('realurl')) {
                            if ($row['tx_sudhaus7newspage_showdate'] > 0) {
                                $fieldArray['tx_realurl_pathsegment'] = date(
                                    'd-m-Y',
                                        $row['tx_sudhaus7newspage_from']
                                ) . '-' . $this->generateslug($fieldArray['title']);
                            } else {
                                $fieldArray['tx_realurl_pathsegment'] = '';
                            }
                            $this->deleteRealurlCache($pages_language_overlay['pid']);
                        }
                        $this->cacheManager->flushCachesByTag('pageId_'.$row['pid']);
                    }
                }
            }
            if ($table == 'tt_content') {
                $this->handleTTcontent($id, $fieldArray);
            }
        }
    }

    private function generateslug($str)
    {
        $str = strtolower(trim($str));

        $str = preg_replace('~[^\\pL\d]+~u', '-', $str);
        $str = str_replace(
            array(
                'ß',
                'ä',
                'ü',
                'ö',
            ),
            array(
                'ss',
                'ae',
                'ue',
                'oe',
            ),
            $str
        );
        // Trim incl. dashes
        $str = trim($str, '-');
        if (function_exists('iconv') === true) {
            $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);
        }
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);

        return $str;
    }

    private function getMetaIfNotSet($row, $fieldArray)
    {
        $aRet = array();

        if (isset($fieldArray['sys_language_uid'])) {
            $aRet[] = $fieldArray['sys_language_uid'];
        } else {
            $aRet[] = $row['sys_language_uid'];
        }
        if (isset($fieldArray['tx_sudhaus7newspage_from'])) {
            $aRet[] = $fieldArray['tx_sudhaus7newspage_from'];
        } else {
            $aRet[] = $row['tx_sudhaus7newspage_from'];
        }
        if (isset($fieldArray['pid'])) {
            $aRet[] = $fieldArray['pid'];
        } else {
            $aRet[] = $row['pid'];
        }
        return $aRet;
    }

    /**
     * @param $id
     * @param $fieldArray
     */
    private function handleTTcontent($id, &$fieldArray)
    {
        $row = BackendUtility::getRecord('tt_content', $id);
        if (!empty($row)) {
            list($sys_language_uid, $tx_sudhaus7newspage_from, $pid) = $this->getMetaIfNotSet(
                $row,
                $fieldArray
            );
            if ($tx_sudhaus7newspage_from > 0) {
                if ($row['sys_language_uid'] > 0) {
                    $pagetable = 'pages_language_overlay';
                    
                    $page = $this->databaseConnection->select(['*'], $pagetable, ['pid'=>$pid,'sys_language_uid'=>$sys_language_uid])->fetch(\PDO::FETCH_ASSOC);
                } else {
                    $pagetable = 'pages';
                    $page = $this->databaseConnection->select(['*'], $pagetable, ['uid'=>$pid])->fetch(\PDO::FETCH_ASSOC);
                }
                if ($page) {
                    if ($this->pm->isPackageAvailable('realurl')) {
                        $tx_realurl_pathsegment = '';
                        $showdate = isset($fieldArray['tx_sudhaus7newspage_showdate'])
                            ? $fieldArray['tx_sudhaus7newspage_showdate']
                            : $row['tx_sudhaus7newspage_showdate'];
                        if ($showdate > 0) {
                            $tx_realurl_pathsegment = date(
                                'd-m-Y',
                                    isset($fieldArray['tx_sudhaus7newspage_from'])
                                        ? $fieldArray['tx_sudhaus7newspage_from']
                                        : $row['tx_sudhaus7newspage_from']
                            ) . '-' . $this->generateslug($page['title']);
                        }
                        $this->databaseConnection->update($pagetable, ['tx_realurl_pathsegment' => $tx_realurl_pathsegment], ['uid'=>$page['uid']]);
                        $this->deleteRealurlCache($pid);
                    }
                    $this->cacheManager->flushCachesByTag('pageId_' . $pid);
                }
            }
        }
    }
    
    /**
     * @param $pid
     * @throws \TYPO3\CMS\Core\Package\Exception\UnknownPackageException
     */
    private function deleteRealurlCache($pid)
    {
        $realurl_version = $this->pm->getPackage('realurl')
                                    ->getValueFromComposerManifest()->version;
        if (
            explode('.', $realurl_version)[0] < 2 ||
            (explode('.', $realurl_version)[0] == 2 && explode('.', $realurl_version)[1] == 0)
        ) {
            $this->databaseConnection->delete('tx_realurl_pathcache', ['page_id',$pid]);
        } else {
            $this->databaseConnection->delete('tx_realurl_pathdata', ['page_id',$pid]);
        }
    }
}
