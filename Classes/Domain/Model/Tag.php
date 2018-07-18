<?php
namespace SUDHAUS7\Sudhaus7Newspage\Domain\Model;

/**
 * Class Tag
 *
 * @package SUDHAUS7\Sudhaus7Newspage\Domain\Model
 */
class Tag extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{


    /**
     * @var \SUDHAUS7\Sudhaus7Newspage\Domain\Repository\TtContentRepository
     * @inject
     */
    protected $contentRepository;

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';


    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    
    /**
     * Relation
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent> $relation
     * @lazy
     */
    protected $relation;


    /**
     * Returns the relation
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent> $relation
     */
    public function getRelation()
    {
        $this->relation = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $queryResult = $this->contentRepository->findByTxSudhaus7newspageTag($this);
        if (null !== $queryResult) {
            foreach ($queryResult as $o) {
                $this->relation->attach($o);
            }
        }
        return $this->relation;
    }

    /**
     * Sets the relation
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Sudhaus7Newspage\Domain\Model\TtContent> $relation
     * @return void
     */
    public function setRelation(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $relation)
    {
        $this->relation = $relation;
    }
    
    
    /**
     * ParentTag
     *
     * @var int
     */
    protected $parentTag;
    
    /**
     * Returns the ParentTag
     *
     * @return int $parentTag
     */
    public function getParentTag()
    {
        return $this->parentTag;
    }
    
    /**
     * Sets the ParentTag
     *
     * @param int $parentTag
     * @return void
     */
    public function setParentTag($parentTag)
    {
        $this->parentTag = $parentTag;
    }
    
}
