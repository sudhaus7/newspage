<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 03/12/15
 * Time: 11:30
 */

namespace SUDHAUS7\Sudhaus7Newspage\ViewHelpers;

class TagdropdownViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * @var \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TagRepository
     * @inject
     */
    private $tags;

    /**
     * Make a list for dropdowns etc for tags
     *
     * @param string $parent parenttag
     * @param string $as variable name
     * @return string
     *
     * @throws nothing
     *
     */
    public function render($parent=null, $as='elem')
    {
        if ('x'.$parent != 'x'.(int)$parent) {
            $parenttag = $this->tags->findOneByTitle($parent);
            //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($parent);
            //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($parenttag);
            //$parent= $parenttag->getUid();
        }

        $tags = $this->tags->findByParent($parent);
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($tags);
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->tags);

        $output = '';
        foreach ($tags as $tag) {
            $renderChildrenClosure =  $this->buildRenderChildrenClosure();
            $templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();
            $templateVariableContainer->add($as, $tag);
            $output .= $renderChildrenClosure();
            $templateVariableContainer->remove($as);
        }
        return $output;
    }
}
