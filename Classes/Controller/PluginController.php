<?php
namespace SUDHAUS7\Sudhaus7Newspage\Controller;

use SUDHAUS7\Sudhaus7Base\Tools\Globals;
use SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\CacheManager;
use \TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Class PluginController
 *
 * @package SUDHAUS7\Sudhaus7Newspage\Controller
 */
class PluginController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    
    /**
     *
     * @var ConnectionPool
     */
    protected $databaseConnection;
    
    /**
     * @var \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository
     *
     */
    protected $content;
    
    /**
     * @param \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository $ttContentRepository
     */
    public function injectTtContentRepository(\SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository $ttContentRepository)
    {
        $this->content = $ttContentRepository;
    }

    /**
     * @var \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TagRepository
     *
     */
    protected $tags;
    
    /**
     * @param \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TagRepository $tagRepository
     */
    public function injectTagRepository(\SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TagRepository $tagRepository)
    {
        $this->tags = $tagRepository;
    }

    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $pageCache = null;

    /**
     * PluginController constructor.
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function __construct()
    {
        parent::__construct();
        $this->pageCache = GeneralUtility::makeInstance(CacheManager::class)->getCache('sudhaus7newspage_pagecache');
        $this->databaseConnection = GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function listAction()
    {
        $alltags = $this->findTags();

        $selectedTag = ['all'];
        if ($this->request->hasArgument('showtags')) {
            $this->settings['tags'] = $this->request->getArgument('showtags');
        }
        if ($this->request->hasArgument('tag')) {
            $selectedTag = GeneralUtility::trimExplode(',', $this->request->getArgument('tag'), true);
            $this->settings['tags'] =  $this->request->getArgument('tag');
        }

        $this->settings['page'] = $this->request->hasArgument('page') ? $this->request->getArgument('page') : 0;
        $this->settings['month'] = $this->request->hasArgument('month') ? $this->request->getArgument('month') : null;
        
        list($pages, $linkMap) = $this->getPageIds();
        $news = $this->content->findNews($pages, $this->settings);
        
        
        if ($this->settings['ignore']) {
            $news = $this->ignoreNews($news);
        }

        if ($this->settings['replaceemptyshorts']) {
            $news = $this->replaceEmptyShorts($news);
        }
        if (!isset($this->settings['latestTemplate'])) {
            $this->settings['latestTemplate'] = 'Default';
        }
        if (!isset($this->settings['listTemplate'])) {
            $this->settings['listTemplate'] = 'Default';
        }
    
    
        
        if (!isset($this->settings['datetimeStringForFilteringNews'])) {
            $this->settings['datetimeStringForFilteringNews'] = 'now';
        }
        
        $this->mapNewspagePid($news, $linkMap);
        
        $this->newsBeforeDisplay_dispatch($news, 'list');

        $this->view->assign('tags', $alltags);
        $this->view->assign('selectedtags', $selectedTag);
        $this->view->assign('settings', $this->settings);
        $this->view->assign('data', $this->configurationManager->getContentObject()->data);
        $this->view->assign('news', $news);
        $this->view->assign('pages', $pages);
        $this->view->assign('pager', $this->generatePager($this->settings['page']));
    }

    private function getPageIds()
    {
        $cacheKey = isset($_GET['cHash']) && !empty($_GET['cHash'])
            ? $_GET['cHash'] . '_' .
              $GLOBALS['TSFE']->id . '_' .
              $GLOBALS['TSFE']->sys_language_uid . '_' .
              $this->configurationManager->getContentObject()->data['uid']
            : 'pageId_' . $GLOBALS['TSFE']->id . '_' .
              $GLOBALS['TSFE']->sys_language_uid . '_' .
              $this->configurationManager->getContentObject()->data['uid'];
        
        if ($this->pageCache && $a = $this->pageCache->get($cacheKey)) {
            return $a;
        }

        $config = $this->configurationManager->getConfiguration('Framework');
        //$list = $config['persistence']['storagePid'];

        $ids = isset($config['settings']['storagePid']) && !empty($config['settings']['storagePid'])
            ? $config['settings']['storagePid']
            :  $this->configurationManager->getContentObject()->data['pid'];
        $depth = isset($config['settings']['recursive']) && !empty($config['settings']['recursive'])
            ? $config['settings']['recursive']
            : 6;

        $list =  $this->content->getTreeList($ids, $depth);
        
        if (empty($list)) {
            $list = $GLOBALS['TSFE']->rootLine[0]['uid'];
        }
        
        
        
        
        
        if ($GLOBALS['TSFE']->sys_language_uid === 0) {
            $res = $this->databaseConnection->getQueryBuilderForTable('tt_content')
                ->select('pid')
                ->from('tt_content')
                ->where('ctype="sudhaus7newspage_element" '.
                    'and pid in ('.implode(',', $list).') '.
                    'and sys_language_uid= '.$GLOBALS['TSFE']->sys_language_uid.
                    $this->configurationManager->getContentObject()->enableFields('tt_content'))
                ->groupBy('pid')
                ->execute();
        } else {
            $enablefields = $this->configurationManager->getContentObject()->enableFields('tt_content');
            $enablefields2 = str_replace('tt_content', 'tt2', $enablefields);
            $sql = 'select distinct tt_content.pid '.
                    'from tt_content '.
                      'join tt_content tt2 on tt2.uid=tt_content.l18n_parent '.$enablefields2.
                    'where tt_content.ctype="sudhaus7newspage_element" '.
                        'and tt_content.deleted=0 '.
                        'and tt_content.hidden=0 '.
                        'and tt_content.pid in ('.implode(',', $list).')  '.
                        'and tt_content.sys_language_uid= '.$GLOBALS['TSFE']->sys_language_uid.$enablefields;

            $res =  $GLOBALS['TYPO3_DB']->sql_query($sql);
        }
        $rootid = $GLOBALS['TSFE']->rootLine[0]['uid'];
        foreach ($GLOBALS['TSFE']->rootLine as $p) {
            if ($p['is_siteroot']) {
                $rootid = $p['uid'];
            }
        }

        $result = array();
        $clear_array_keys = array('sudhaus7newspage_element_root_'.$rootid,'pageId_' . $GLOBALS['TSFE']->id);
        while ($row = $res->fetchAll()) {
            $result[]=$row[0];
            $clear_array_keys[] = 'pageId_'.$row[0];
        }
        $newsPageTypes = $this->databaseConnection->getQueryBuilderForTable('pages')
            ->select('*')
            ->from('pages')
            ->where('doktype=101 and uid in ('. implode(',', $list).') '.
                $this->configurationManager->getContentObject()->enableFields('pages'))
            ->execute()
            ->fetchAll();

        $linkMapper = [];
        foreach ($newsPageTypes as $page) {
            $result[] = $page['content_from_pid'];
            $clear_array_keys[] = $page['content_from_pid'];
            $linkMapper[$page['content_from_pid']] = $page['uid'];
        }
        if (empty($result)) {
            $result[] = $GLOBALS['TSFE']->rootLine[0]['uid'];
        }
        $complete = [$result,$linkMapper];
        if ($this->pageCache) {
            $this->pageCache->set($cacheKey, $complete, $clear_array_keys);
        }

        return  $complete;
    }
    
    /**
     * Ignore News, needs to be refactored into limit - this belongs to the Repository
     *
     * @param $news
     * @return mixed
     */
    private function ignoreNews($news)
    {
        $news = $news->toArray();

        /* TO BE REMOVED
        if ($this->settings['onlyIgnoreHighlights']) {
            $counter = 0;
            $news_new = array();
            foreach ($news as $record) {
                if ($counter < $this->settings['ignore']) {
                    if ($record->getTxSudhaus7newspageHighlight()) {
                        $counter++;
                    } else {
                        $news_new[] = $record;
                    }
                } else {
                    $news_new[] = $record;
                }
            }
            return $news_new;
        } else {
            array_splice($news, 0, $this->settings['ignore']);
            return $news;
        }
        */
        array_splice($news, 0, $this->settings['ignore']);
        return $news;
    }

    public function injectContent(\SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository $content)
    {
        $this->content = $content;
    }

    public function injectTags(\SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TagRepository $tags)
    {
        $this->tags = $tags;
    }

    private function replaceEmptyShorts($news)
    {
        /**
         * @var $msg \SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent
         */
        foreach ($news as $msg) {
            $short = $msg->getBodytext();
            $short = trim($short);
            if (empty($short)) {
                $content = $this->content->findNextContent($msg)->getFirst();
                if (!empty($content)) {
                    $txt = strip_tags($content->getBodytext());
                    $txt = preg_replace('/\s+/', ' ', $txt);
                    // Das soll im Template passieren, nicht hier
                    // $txt = substr($txt, 0, 160);
                    // $a = explode(' ', $txt);
                    // array_pop($a);
                    $msg->setBodytext($txt);
                }
            }
        }
        return $news;
    }
    
    /**
     * @param $page
     * @return array
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    private function generatePager($page)
    {
        list($pages, $linkMap) = $this->getPageIds();
        $max = $this->content->findNewsMax($pages, $this->settings);
        $return = array(
            'prev' => array(
                'page'=>($page-$this->settings['max'] < 0 ? 0 : $page-$this->settings['max']),
                'linkconfig'=>array(
                    'page'=>($page-$this->settings['max'] < 0 ? 0 : $page-$this->settings['max']),
                )
            ),
            'next' => array(
               'page'=>($page+$this->settings['max'] >= $max ? 0 : $page+$this->settings['max']),
                'linkconfig'=>array(
                    'page'=>($page+$this->settings['max'] >= $max ? 0 : $page+$this->settings['max']),
                )
            ),
            'pages'=>array(),
            'current'=>$page
        );
        if ($this->request->hasArgument('month')) {
            $return['prev']['month'] = $this->request->getArgument('month');
            $return['next']['month'] = $this->request->getArgument('month');
            $return['prev']['linkconfig']['month'] = $this->request->getArgument('month');
            $return['next']['linkconfig']['month'] = $this->request->getArgument('month');
        }
        if ($this->request->hasArgument('tag')) {
            $return['prev']['tag'] = $this->request->getArgument('tag');
            $return['next']['tag'] = $this->request->getArgument('tag');
            $return['prev']['linkconfig']['tag'] = $this->request->getArgument('tag');
            $return['next']['linkconfig']['tag'] = $this->request->getArgument('tag');
        }


        $idx = 1;
        for ($i=0 ; $i<$max ; $i=$i+$this->settings['max']) {
            $return['pages'][$idx] = array(
                'label'=>$idx,
                'page'=>$i,
                'isactive'=>$i==$page,
                'linkconfig'=>array(
                    'page'=>$i,
                )
            );
            if ($this->request->hasArgument('month')) {
                $return['pages'][$idx]['month'] = $this->request->getArgument('month');
                $return['pages'][$idx]['month'] = $this->request->getArgument('month');
                $return['pages'][$idx]['linkconfig']['month'] = $this->request->getArgument('month');
                $return['pages'][$idx]['linkconfig']['month'] = $this->request->getArgument('month');
            }
            if ($this->request->hasArgument('tag')) {
                $return['pages'][$idx]['tag'] = $this->request->getArgument('tag');
                $return['pages'][$idx]['tag'] = $this->request->getArgument('tag');
                $return['pages'][$idx]['linkconfig']['tag'] = $this->request->getArgument('tag');
                $return['pages'][$idx]['linkconfig']['tag'] = $this->request->getArgument('tag');
            }
            $idx++;
        }
        return $return;
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function latestAction()
    {
        $alltags = $this->findTags();
        $this->settings['page'] = 0;
        $this->settings['month'] = $this->request->hasArgument('month')
            ? $this->request->getArgument('month')
            : null;

        /** @var \TYPO3\CMS\Extbase\Persistence\QueryInterface $news */
        if (!isset($this->settings['latestTemplate'])) {
            $this->settings['latestTemplate'] = 'Default';
        }
        if (!isset($this->settings['listTemplate'])) {
            $this->settings['listTemplate'] = 'Default';
        }


        list($pages, $linkMap) = $this->getPageIds();
        $news = $this->content->findNews($pages, $this->settings);

        
        if ($this->settings['ignore']) {
            $news = $this->ignoreNews($news);
        }
        if ($this->settings['replaceemptyshorts']) {
            $news = $this->replaceEmptyShorts($news);
        }

        $this->mapNewspagePid($news, $linkMap);

        $this->newsBeforeDisplay_dispatch($news, 'latest');

        $this->view->assign('tags', $alltags);
        $this->view->assign('settings', $this->settings);
        $this->view->assign('data', $this->configurationManager->getContentObject()->data);
        $this->view->assign('news', $news);
        $this->view->assign('pages', $pages);
    }
    
    /**
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function rssAction()
    {
        //$news = $this->fetchNews(0);
        $this->settings['page'] = $this->request->hasArgument('page')
            ? $this->request->getArgument('page')
            : 0;
        $this->settings['month'] = $this->request->hasArgument('month')
            ? $this->request->getArgument('month')
            : null;

        list($pages, $linkMap) = $this->getPageIds();
        $news = $this->content->findNews($pages, $this->settings);
      
        
        $this->mapNewspagePid($news, $linkMap);
        $newscontainer = [];
        foreach ($news as $rec) {
            $record = ['news'=>$rec,'content'=>''];
            if (isset($this->settings['fullrss']) && !empty($this->settings['fullrss'])) {
                $content = Globals::db()->exec_SELECTgetRows(
                    '*',
                    'tt_content',
                    'ctype in ("text","textmedia","header")  '.
                       ' and deleted=0  '.
                       ' and hidden=0  '.
                       ' and pid='.$rec->getPid(),
                    '',
                    'sorting asc'
                );
                foreach ($content as $c) {
                    $record['content'].='<p>&nbsp;</p>';
                    if (!empty($c['header'])) {
                        $record['content'].='<p><strong>'.$c['header'].'</strong></p><br/>';
                    }
                    if (!empty($c['bodytext'])) {
                        $record['content'].= '<p>'.$c['bodytext'].'</p>';
                    }
                }
            }
            $newscontainer[]=$record;
        }
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($newscontainer); exit;

        $this->view->assign('mybaseurl', 'http://'.$_SERVER['HTTP_HOST'].'/');
        $this->view->assign('data', $this->configurationManager->getContentObject()->data);
        $this->view->assign('news', $newscontainer);
    }

    /**
     * @return array|null|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    protected function findTags()
    {
        $alltags = null;
        if (!is_null($this->tags)) {
            list($pages, $linkMap) = $this->getPageIds();
            $alltags = $this->tags->findAllList($this->settings['tags'], $pages);
        }
        return $alltags;
    }

    private function mapNewspagePid(&$news, $linkMap)
    {
        /** @var TtContent $new */
        foreach ($news as $new) {
            if (array_key_exists($new->getPid(), $linkMap)) {
                $new->setPid($linkMap[$new->getPid()]);
            }
        }
    }


    /**
     * @param array $news
     * @param $action
     *
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    private function newsBeforeDisplay_dispatch(&$news, $action)
    {
        /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        $data = ['news'=>$news,'settings'=>$this->settings,'action'=>$action];
        $ret = $signalSlotDispatcher->dispatch(__CLASS__, 'newsBeforeDisplay', [$data]);
        $news = $ret[0]['news'];
    }
}
