<?php
namespace SUDHAUS7\Sudhaus7Newspage\Controller;

use SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository;

class ElementController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


    /**
     * @var \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository
     */
    protected $content;
    
    /**
     * @param TtContentRepository $ttContentRepository
     */
    public function injectTtContentRepository(\SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository $ttContentRepository)
    {
        $this->content = $ttContentRepository;
    }
    
    
    public function showAction()
    {
        /** @var \SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent $record */
        $record = $this->content->findByUid($this->configurationManager->getContentObject()->data['uid']);
        $this->view->assign('settings', $this->configurationManager->getConfiguration('Settings'));
        $this->view->assign('data', $this->configurationManager->getContentObject()->data);
        $this->view->assign('record', $record);
    }
}
