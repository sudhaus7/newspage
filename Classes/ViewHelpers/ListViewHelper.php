<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 18/11/2016
 * Time: 09:19
 */

namespace SUDHAUS7\Sudhaus7Newspage\ViewHelpers;

use TYPO3\CMS\Core\Utility\ArrayUtility;

class ListViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    private $db = null;

    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    private $tsfe = null;

    private $configuration = array(
        'mode'=>'list',
        'count'=>3,
        'skip'=>0,
        'skiptype'=>'latest',
        'newstype'=>1,
    );

    public function initializeArguments()
    {
        $this->registerArgument('configuration', 'array', 'Config', false, array());
        $this->registerArgument('as', 'string', 'records', false, 'value');
    }

    public function render()
    {
        $this->db = $GLOBALS['TYPO3_DB'];
        $this->tsfe = $GLOBALS['TSFE'];

        ArrayUtility::mergeRecursiveWithOverrule($this->configuration, $this->arguments['configuration'], false);

        $records = array();
        $method = 'fetch'.ucfirst($this->configuration['mode']);
        if (method_exists($this, $method)) {
            $uidlist = $this->$method();
        }

        $renderChildrenClosure = $this->buildRenderChildrenClosure();
        $templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();
        $templateVariableContainer->add($this->arguments['as'], $records);
        $output = $renderChildrenClosure();
        $templateVariableContainer->remove($this->arguments['as']);

        return $output;
    }

    private function fetchList()
    {
        $sql = 'select uid from tt_content
          join pages on tt_content.pid = pages.uid '.$this->tsfe->cObj->enableFields('pages').'
          where 
          tt_content.ctype="sudhaus7newspage_element" 
          and tt_content.deleted=0 and tt_content.hidden=0
          and tt_content.tx_sudhaus7newspage_type in ('.$this->configuration['newstype'].') 
          '.$this->tsfe->cObj->enableFields('tt_content').' order by tx_sudhaus7newspage_from desc
          limit '.$this->configuration['skip'].','.$this->configuration['count'].'
          ';
        $res = $this->db->sql_query($sql);
        $list = array();
        while ($row = $this->db->sql_fetch_row($res)) {
            $list[]=$row[0];
        }
        return $list;
    }

    private function fetchRandom()
    {
        $skip = [];
        if ($this->configuration['skip'] > 0) {
            $sql = 'select uid from tt_content
          join pages on tt_content.pid = pages.uid '.$this->tsfe->cObj->enableFields('pages').'
          where 
          tt_content.ctype="sudhaus7newspage_element" 
          and tt_content.deleted=0 and tt_content.hidden=0
          and tt_content.tx_sudhaus7newspage_type in ('.$this->configuration['newstype'].') 
          '.$this->tsfe->cObj->enableFields('tt_content').' order by tx_sudhaus7newspage_from desc
          limit 0,'.$this->configuration['skip'].'
          ';
            $res = $this->db->sql_query($sql);
            $list = array();
            while ($row = $this->db->sql_fetch_row($res)) {
                $skip[]=$row[0];
            }
        }

        $sql = 'select uid from tt_content
          join pages on tt_content.pid = pages.uid '.$this->tsfe->cObj->enableFields('pages').'
          where 
          tt_content.ctype="sudhaus7newspage_element" 
          and tt_content.deleted=0 and tt_content.hidden=0';
        if (empty($skip)) {
        }

        $sql .= 'and tt_content.tx_sudhaus7newspage_type in ('.$this->configuration['newstype'].') 
          '.$this->tsfe->cObj->enableFields('tt_content').' order by tx_sudhaus7newspage_from desc
          limit 0,'.$this->configuration['skip'].'
          ';
    }
}
