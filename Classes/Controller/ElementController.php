<?php
namespace SUDHAUS7\Sudhaus7Newspage\Controller;

class ElementController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{


    /**
     * @var \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository
     * @inject
     */
    protected $content;

    public function showAction()
    {
        /** @var \SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent $data */
        $data = $this->content->findByUid($this->configurationManager->getContentObject()->data['uid']);
        $this->view->assign('settings', $this->configurationManager->getConfiguration('Settings'));
        $this->view->assign('data', $this->configurationManager->getContentObject()->data);
        $this->view->assign('record', $data);
    }
}
