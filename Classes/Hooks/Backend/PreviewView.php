<?php

namespace SUDHAUS7\Sudhaus7Newspage\Hooks\Backend;

use SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository;

class PreviewView implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface
{
    /**
     * Preprocesses the preview rendering of a content element.
     *
     * @param  \TYPO3\CMS\Backend\View\PageLayoutView $parentObject Calling parent object
     * @param  boolean $drawItem Whether to draw the item using the default functionalities
     * @param  string $headerContent Header content
     * @param  string $itemContent Item content
     * @param  array $row Record row of tt_content
     *
     * @return void
     */
    public function preProcess(
        \TYPO3\CMS\Backend\View\PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        switch ($row['CType']) {
            case 'sudhaus7newspage_element':
                // sample
                $drawItem = false;

                if (empty($row['tx_sudhaus7newspage_from'])) {
                    $headerContent = '<strong>' . $row['header'] . '</strong><br/>';
                    $itemContent   = $row['bodytext'] . '<br/>';
                } else {
                    $headerContent = '<strong>' . date(
                        'd. M Y',
                            $row['tx_sudhaus7newspage_from']
                    ) . ' : ' . $row['header'] . '</strong><br/>';
                    $itemContent   = $row['bodytext'] . '<br/>';
                }
                list($headerContent, $itemContent) = $this->dispatchSignal(
                    'preProcessElement',
                    $headerContent,
                    $itemContent,
                    $row,
                    $parentObject,
                    $drawItem
                );

                break;
            case 'list':
                if ($row['list_type'] == 'sudhaus7newspage_plugin') {
                    $drawItem      = false;
                    $headerContent = '<strong>' . $GLOBALS['LANG']->sL('LLL:EXT:sudhaus7_newspage/Resources/Private/Language/locallang.xlf:tt_content.newspage_plugin') . '</strong><br/>';
                    $headerContent .= '<strong>' . $row['header'] . '</strong><br/>';

                    list($headerContent, $itemContent) = $this->dispatchSignal(
                        'preProcessListHeader',
                        $headerContent,
                        $itemContent,
                        $row,
                        $parentObject,
                        $drawItem
                    );

                    $this->fetchList($itemContent, $row, $parentObject, $drawItem);
                }
                break;
            default:
                // do nothing
                break;
        }
    }

    /**
     * @param string $signalName
     * @param string $headerContent
     * @param string $itemContent
     * @param array|\SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent $row
     * @param $pObj
     *
     * @return array
     */
    private function dispatchSignal(
        $signalName,
        $headerContent,
        $itemContent,
        &$row,
        \TYPO3\CMS\Backend\View\PageLayoutView &$pObj,
        &$drawItem
    ) {
        /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
        $data                 = [
            'row'           => $row,
            'headerContent' => $headerContent,
            'itemContent'   => $itemContent,
            'pObj'          => $pObj,
            'drawItem'      => $drawItem,
        ];
        $ret                  = $signalSlotDispatcher->dispatch(__CLASS__, $signalName, [ $data ]);
        $headerContent        = $ret[0]['headerContent'];
        $itemContent          = $ret[0]['itemContent'];
        $drawItem             = $ret[0]['drawItem'];

        return [ $headerContent, $itemContent ];
    }

    /**
     * @param $itemContent
     * @param array $row
     * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject
     * @param $drawItem
     */
    private function fetchList(
        &$itemContent,
        array &$row,
        \TYPO3\CMS\Backend\View\PageLayoutView &$parentObject,
        &$drawItem
    ) {
        $extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        /** @var \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository $yourRepository */
        $yourRepository = $extbaseObjectManager->get(TtContentRepository::class);

        $config = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($row['pi_flexform']);

        $settings = [];
        if (isset($config['data']['sDEF']['lDEF'])) {
            foreach ($config['data']['sDEF']['lDEF'] as $k => $v) {
                $key              = explode('.', $k);
                $key              = array_pop($key);
                $settings[ $key ] = $v['vDEF'];
            }
        }
        try {
            $treelist = $yourRepository->getTreeList($settings['storagePid'], $settings['recursive']);
            $elements = $yourRepository->findNews($treelist, $settings);

            /** @var \SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent $element */
            foreach ($elements as $element) {
                if (is_object($element->getTxSudhaus7newspageFrom())) {
                    $content = $element->getTxSudhaus7newspageFrom()
                                       ->format('d. M Y') . ' : ' . $element->getHeader() . '<br/>';
                } else {
                    $content = $element->getHeader() . '<br/>';
                }
                list($headerContent, $content) = $this->dispatchSignal(
                    'preProcessListContent',
                    '',
                    $content,
                    $element,
                    $parentObject,
                    $drawItem
                );
                $itemContent .= $content;
            }
        } catch (\Exception $e) {
        }
    }
}
