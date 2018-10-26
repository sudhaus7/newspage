<?php
namespace SUDHAUS7\Newspage\Domain\Model;

class TtContent extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var \SUDHAUS7\Newspage\Domain\Repository\TagRepository
     * @inject
     */
    protected $contentRepository;

    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * @var string
     */
    protected $ctype;

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $headerPosition;

    /**
     * @var string
     */
    protected $bodytext;

    /**
     * @var integer
     */
    protected $colpos;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $image;

    /**
     * @var integer
     */
    protected $imagewidth;

    /**
     * @var integer
     */
    protected $imageorient;

    /**
     * @var string
     */
    protected $imagecaption;

    /**
     * @var integer
     */
    protected $imagecols;

    /**
     * @var integer
     */
    protected $imageborder;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $media;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var integer
     */
    protected $cols;

    /**
     * @var string
     */
    protected $subheader;

    /**
     * @var string
     */
    protected $headerLink;

    /**
     * @var string
     */
    protected $imageLink;

    /**
     * @var string
     */
    protected $imageZoom;

    /**
     * @var string
     */
    protected $altText;

    /**
     * @var string
     */
    protected $titleText;

    /**
     * @var string
     */
    protected $headerLayout;

    /**
     * @var string
     */
    protected $listType;

    /**
     * @var int
     */
    protected $sorting;

    /**
     * tx_newspage_tag
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Newspage\Domain\Model\Tag>
     * @lazy
     */
    protected $txSudhaus7newspageTag;
    /**
     * Pid
     *
     * @var int
     */
    protected $pid;
    /**
     * tx_newspage_from
     *
     * @var \DateTime
     */
    protected $txSudhaus7newspageFrom;
    /**
     * tx_newspage_to
     *
     * @var \DateTime
     */
    protected $txSudhaus7newspageTo;
    /**
     * TxSudhaus7newspageType
     *
     * @var int
     */
    protected $txSudhaus7newspageType;
    /**
     * TxSudhaus7newspageShowdate
     *
     * @var int
     */
    protected $txSudhaus7newspageShowdate;
    /**
     * TxSudhaus7newspageShowtime
     *
     * @var int
     */
    protected $txSudhaus7newspageShowtime;
    /**
     * TxSudhaus7newspageHighlight
     *
     * @var int
     */
    protected $txSudhaus7newspageHighlight;
    /**
     * TxSudhaus7newspagePlace
     *
     * @var \string
     */
    protected $txSudhaus7newspagePlace;
    /**
     * TxSudhaus7newspageWho
     *
     * @var string
     */
    protected $txSudhaus7newspageWho;

    /**
     * @var bool
     */
    protected $txSudhaus7newspageShowimageindetail;

    /**
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * @param \DateTime $crdate
     * @return void
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * @param \DateTime $tstamp
     * @return void
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }

    /**
     * @return string
     */
    public function getCtype()
    {
        return $this->ctype;
    }

    /**
     * @param $ctype
     * @return void
     */
    public function setCtype($ctype)
    {
        $this->ctype = $ctype;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param $header
     * @return void
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getHeaderPosition()
    {
        return $this->headerPosition;
    }

    /**
     * @param $headerPosition
     * @return void
     */
    public function setHeaderPosition($headerPosition)
    {
        $this->headerPosition = $headerPosition;
    }

    /**
     * @return string
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * @param $bodytext
     * @return void
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Get the colpos
     *
     * @return integer
     */
    public function getColPos()
    {
        return (int)$this->colpos;
    }

    /**
     * Set colpos
     *
     * @param integer $colpos
     * @return void
     */
    public function setColPos($colpos)
    {
        $this->colpos = $colpos;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $image
     * @return void
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getImagewidth()
    {
        return $this->imagewidth;
    }

    /**
     * @param $imagewidth
     * @return void
     */
    public function setImagewidth($imagewidth)
    {
        $this->imagewidth = $imagewidth;
    }

    /**
     * @return int
     */
    public function getImageorient()
    {
        return $this->imageorient;
    }

    /**
     * @param $imageorient
     * @return void
     */
    public function setImageorient($imageorient)
    {
        $this->imageorient = $imageorient;
    }

    /**
     * @return string
     */
    public function getImagecaption()
    {
        return $this->imagecaption;
    }

    /**
     * @param $imagecaption
     * @return void
     */
    public function setImagecaption($imagecaption)
    {
        $this->imagecaption = $imagecaption;
    }

    /**
     * @return int
     */
    public function getImagecols()
    {
        return $this->imagecols;
    }

    /**
     * @param $imagecols
     * @return void
     */
    public function setImagecols($imagecols)
    {
        $this->imagecols = $imagecols;
    }

    /**
     * @return int
     */
    public function getImageborder()
    {
        return $this->imageborder;
    }

    /**
     * @param $imageborder
     * @return void
     */
    public function setImageborder($imageborder)
    {
        $this->imageborder = $imageborder;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $media
     * @return void
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return int
     */
    public function getCols()
    {
        return $this->cols;
    }

    /**
     * @param $cols
     * @return void
     */
    public function setCols($cols)
    {
        $this->cols = $cols;
    }

    /**
     * @return string
     */
    public function getSubheader()
    {
        return $this->subheader;
    }

    /**
     * @param $subheader
     * @return void
     */
    public function setSubheader($subheader)
    {
        $this->subheader = $subheader;
    }

    /**
     * @return string
     */
    public function getHeaderLink()
    {
        return $this->headerLink;
    }

    /**
     * @param $headerLink
     * @return void
     */
    public function setHeaderLink($headerLink)
    {
        $this->headerLink = $headerLink;
    }

    /**
     * @return string
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @param $imageLink
     * @return void
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
    }

    /**
     * @return string
     */
    public function getImageZoom()
    {
        return $this->imageZoom;
    }

    /**
     * @param $imageZoom
     * @return void
     */
    public function setImageZoom($imageZoom)
    {
        $this->imageZoom = $imageZoom;
    }

    /**
     * @return string
     */
    public function getAltText()
    {
        return $this->altText;
    }

    /**
     * @param $altText
     * @return void
     */
    public function setAltText($altText)
    {
        $this->altText = $altText;
    }

    /**
     * @return string
     */
    public function getTitleText()
    {
        return $this->titleText;
    }

    /**
     * @param $titleText
     * @return void
     */
    public function setTitleText($titleText)
    {
        $this->titleText = $titleText;
    }

    /**
     * @return string
     */
    public function getHeaderLayout()
    {
        return $this->headerLayout;
    }

    /**
     * @param $headerLayout
     * @return void
     */
    public function setHeaderLayout($headerLayout)
    {
        $this->headerLayout = $headerLayout;
    }

    /**
     * @return string
     */
    public function getListType()
    {
        return $this->listType;
    }

    /**
     * @param $listType
     * @return void
     */
    public function setListType($listType)
    {
        $this->listType = $listType;
    }

    /**
     * Returns the tag
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Newspage\Domain\Model\Tag> $txSudhaus7newspageTag
     * @lazy
     */
    public function getTxSudhaus7newspageTag()
    {
        return $this->txSudhaus7newspageTag;
    }

    /**
     * Sets the relation
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\SUDHAUS7\Newspage\Domain\Model\Tag> $txSudhaus7newspageTag
     * @return void
     */
    public function setTxSudhaus7newspageTag(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $txSudhaus7newspageTag)
    {
        $this->txSudhaus7newspageTag = $txSudhaus7newspageTag;
    }

    /**
     * Returns the Pid
     *
     * @return int $pid
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Sets the Pid
     *
     * @param int $pid
     * @return void
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return \DateTime
     */
    public function getTxSudhaus7newspageFrom()
    {
        return $this->txSudhaus7newspageFrom;
    }

    /**
     * @param \DateTime $txSudhaus7newspageFrom
     * @return void
     */
    public function setTxSudhaus7newspageFrom($txSudhaus7newspageFrom)
    {
        $this->txSudhaus7newspageFrom = $txSudhaus7newspageFrom;
    }

    /**
     * @return \DateTime
     */
    public function getTxSudhaus7newspageTo()
    {
        return $this->txSudhaus7newspageTo;
    }

    /**
     * @param \DateTime $txSudhaus7newspageTo
     * @return void
     */
    public function setTxSudhaus7newspageTo($txSudhaus7newspageTo)
    {
        $this->txSudhaus7newspageTo = $txSudhaus7newspageTo;
    }

    /**
     * Returns the TxSudhaus7newspageType
     *
     * @return int $txSudhaus7newspageType
     */
    public function getTxSudhaus7newspageType()
    {
        return $this->txSudhaus7newspageType;
    }
    
    /**
     * Sets the TxSudhaus7newspageType
     *
     * @param int $txSudhaus7newspageType
     * @return void
     */
    public function setTxSudhaus7newspageType($txSudhaus7newspageType)
    {
        $this->txSudhaus7newspageType = $txSudhaus7newspageType;
    }
    
    /**
     * Returns the TxSudhaus7newspageShowdate
     *
     * @return int $txSudhaus7newspageShowdate
     */
    public function getTxSudhaus7newspageShowdate()
    {
        return $this->txSudhaus7newspageShowdate;
    }
    
    /**
     * Sets the TxSudhaus7newspageShowdate
     *
     * @param int $txSudhaus7newspageShowdate
     * @return void
     */
    public function setTxSudhaus7newspageShowdate($txSudhaus7newspageShowdate)
    {
        $this->txSudhaus7newspageShowdate = $txSudhaus7newspageShowdate;
    }
    
    /**
     * Returns the TxSudhaus7newspageShowtime
     *
     * @return int $txSudhaus7newspageShowtime
     */
    public function getTxSudhaus7newspageShowtime()
    {
        return $this->txSudhaus7newspageShowtime;
    }
    
    /**
     * Sets the TxSudhaus7newspageShowtime
     *
     * @param int $txSudhaus7newspageShowtime
     * @return void
     */
    public function setTxSudhaus7newspageShowtime($txSudhaus7newspageShowtime)
    {
        $this->txSudhaus7newspageShowtime = $txSudhaus7newspageShowtime;
    }

    /**
     * Returns the TxSudhaus7newspageHighlight
     *
     * @return int $txSudhaus7newspageHighlight
     */
    public function getTxSudhaus7newspageHighlight()
    {
        return $this->txSudhaus7newspageHighlight;
    }

    /**
     * Sets the TxSudhaus7newspageHighlight
     *
     * @param int $txSudhaus7newspageHighlight
     * @return void
     */
    public function setTxSudhaus7newspageHighlight($txSudhaus7newspageHighlight)
    {
        $this->txSudhaus7newspageHighlight = $txSudhaus7newspageHighlight;
    }

    /**
     * Returns the TxSudhaus7newspagePlace
     *
     * @return string $txSudhaus7newspagePlace
     */
    public function getTxSudhaus7newspagePlace()
    {
        return $this->txSudhaus7newspagePlace;
    }

    /**
     * Sets the TxSudhaus7newspagePlace
     *
     * @param string $txSudhaus7newspagePlace
     * @return void
     */
    public function setTxSudhaus7newspagePlace($txSudhaus7newspagePlace)
    {
        $this->txSudhaus7newspagePlace = $txSudhaus7newspagePlace;
    }

    /**
     * Returns the TxSudhaus7newspageWho
     *
     * @return string $txSudhaus7newspageWho
     */
    public function getTxSudhaus7newspageWho()
    {
        return $this->txSudhaus7newspageWho;
    }

    /**
     * Sets the TxSudhaus7newspageWho
     *
     * @param string $txSudhaus7newspageWho
     * @return void
     */
    public function setTxSudhaus7newspageWho($txSudhaus7newspageWho)
    {
        $this->txSudhaus7newspageWho = $txSudhaus7newspageWho;
    }


    /**
     * Returns the sorting
     *
     * @return int $sorting
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Sets the sorting
     *
     * @param int $sorting
     * @return void
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * Returns the TxSudhaus7newspageShowimageindetail
     *
     * @return bool $txSudhaus7newspageShowimageindetail
     */
    public function getTxSudhaus7newspageShowimageindetail()
    {
        return $this->txSudhaus7newspageShowimageindetail;
    }

    /**
     * Sets the TxSudhaus7newspageShowimageindetail
     *
     * @param bool $txSudhaus7newspageShowimageindetail
     * @return void
     */
    public function setTxSudhaus7newspageShowimageindetail($txSudhaus7newspageShowimageindetail)
    {
        $this->txSudhaus7newspageShowimageindetail = $txSudhaus7newspageShowimageindetail;
    }
    
    /**
     * TxSudhaus7newspageLatlng
     *
     * @var \string
     */
    protected $txSudhaus7newspageLatlng;
    
    /**
     * Returns the TxSudhaus7newspageLatlng
     *
     * @return \string $txSudhaus7newspageLatlng
     */
    public function getTxSudhaus7newspageLatlng()
    {
        return $this->txSudhaus7newspageLatlng;
    }
    
    /**
     * Sets the TxSudhaus7newspageLatlng
     *
     * @param \string $txSudhaus7newspageLatlng
     * @return void
     */
    public function setTxSudhaus7newspageLatlng($txSudhaus7newspageLatlng)
    {
        $this->txSudhaus7newspageLatlng = $txSudhaus7newspageLatlng;
    }
}
