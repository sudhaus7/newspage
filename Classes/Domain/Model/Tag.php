<?php
namespace SUDHAUS7\Newspage\Domain\Model;

/**
 * Class Tag
 *
 * @package SUDHAUS7\Newspage\Domain\Model
 */
class Tag extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{


    /**
     * @var \SUDHAUS7\Newspage\Domain\Repository\TtContentRepository
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
     * Category
     *
     * @var string
     */
    protected $category = '';


    /**
     * Returns the category
     *
     * @return string $category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the category
     *
     * @param string $category
     *
     * @return void
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * Relation
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Newspage\Domain\Model\TtContent> $relation
     * @lazy
     */
    protected $relation;


    /**
     * Returns the relation
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Newspage\Domain\Model\TtContent> $relation
     */
    public function getRelation()
    {
        $this->relation = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $queryResult    = $this->contentRepository->findByTxSudhaus7newspageTag($this);
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
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Newspage\Domain\Model\TtContent> $relation
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


    /**
     * Map
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $map;

    /**
     * Returns the Map
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $map
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Sets the Map
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>  $map
     *
     * @return void
     */
    public function setMap($map)
    {
        $this->map = $map;
    }


    /**
     * Geodata
     *
     * @var string
     */
    protected $geodata;

    /**
     * Returns the Geodata
     *
     * @return string $geodata
     */
    public function getGeodata()
    {
        return $this->geodata;
    }

    /**
     * Sets the Geodata
     *
     * @param string $geodata
     *
     * @return void
     */
    public function setGeodata($geodata)
    {
        $this->geodata = $geodata;
    }


    /**
     * Georatio
     *
     * @var int
     */
    protected $georatio;

    /**
     * Returns the Georatio
     *
     * @return int $georatio
     */
    public function getGeoratio()
    {
        return $this->georatio;
    }

    /**
     * Sets the Georatio
     *
     * @param int $georatio
     *
     * @return void
     */
    public function setGeoratio($georatio)
    {
        $this->georatio = $georatio;
    }


    /**
     * Countrydesc
     *
     * @var string
     */
    protected $countrydesc;

    /**
     * Returns the Countrydesc
     *
     * @return string $countrydesc
     */
    public function getCountrydesc()
    {
        return $this->countrydesc;
    }

    /**
     * Sets the Countrydesc
     *
     * @param string $countrydesc
     *
     * @return void
     */
    public function setCountrydesc($countrydesc)
    {
        $this->countrydesc = $countrydesc;
    }


    /**
     * Churchdesc
     *
     * @var string
     */
    protected $churchdesc;

    /**
     * Returns the Churchdesc
     *
     * @return string $churchdesc
     */
    public function getChurchdesc()
    {
        return $this->churchdesc;
    }

    /**
     * Sets the Churchdesc
     *
     * @param string $churchdesc
     *
     * @return void
     */
    public function setChurchdesc($churchdesc)
    {
        $this->churchdesc = $churchdesc;
    }


    /**
     * Staffdesc
     *
     * @var string
     */
    protected $staffdesc;

    /**
     * Returns the Staffdesc
     *
     * @return string $staffdesc
     */
    public function getStaffdesc()
    {
        return $this->staffdesc;
    }

    /**
     * Sets the Staffdesc
     *
     * @param string $staffdesc
     *
     * @return void
     */
    public function setStaffdesc($staffdesc)
    {
        $this->staffdesc = $staffdesc;
    }


    /**
     * Icon
     *
     * @var \string
     */
    protected $icon;

    /**
     * Returns the Icon
     *
     * @return \string $icon
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Sets the Icon
     *
     * @param \string $icon
     *
     * @return void
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }
}
