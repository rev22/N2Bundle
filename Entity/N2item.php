<?php

namespace Unislug\N2Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * N2item
 *
 * @ORM\Table(name="n2Item")
 * @ORM\Entity
 */
class N2item
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published", type="datetime", nullable=true)
     */
    private $published;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=false)
     */
    private $updated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires", type="datetime", nullable=true)
     */
    private $expires;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=3, nullable=true)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="zonename", type="string", length=50, nullable=true)
     */
    private $zonename;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="sortorder", type="integer", nullable=true)
     */
    private $sortorder;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(name="savedby", type="string", length=50, nullable=true)
     */
    private $savedby;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="integer", nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="ancestraltrail", type="string", length=100, nullable=true)
     */
    private $ancestraltrail;

    /**
     * @var integer
     *
     * @ORM\Column(name="versionindex", type="integer", nullable=true)
     */
    private $versionindex;

    /**
     * @var integer
     *
     * @ORM\Column(name="alteredpermissions", type="integer", nullable=true)
     */
    private $alteredpermissions;

    /**
     * @var \N2item
     *
     * @ORM\ManyToOne(targetEntity="N2item", inversedBy="versions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="versionofid", referencedColumnName="id")
     * })
     */
    private $versionofid;

    /**
     * @var \N2item
     *
     * @ORM\ManyToOne(targetEntity="N2item", inversedBy="translations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="translationofid", referencedColumnName="id")
     * })
     */
    private $translationofid;

    /**
     * @var \N2item
     *
     * @ORM\ManyToOne(targetEntity="N2item", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parentid", referencedColumnName="id")
     * })
     */
    private $parentid;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return N2item
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return N2item
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set published
     *
     * @param \DateTime $published
     * @return N2item
     */
    public function setPublished(\DateTime $published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return \DateTime 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return N2item
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set expires
     *
     * @param \DateTime $expires
     * @return N2item
     */
    public function setExpires(\DateTime $expires)
    {
        $this->expires = $expires;
    
        return $this;
    }

    /**
     * Get expires
     *
     * @return \DateTime 
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return N2item
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set language code
     *
     * @param string $lang
     * @return N2item
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get language code
     *
     * @return string 
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set zonename
     *
     * @param string $zonename
     * @return N2item
     */
    public function setZonename($zonename)
    {
        $this->zonename = $zonename;
    
        return $this;
    }

    /**
     * Get zonename
     *
     * @return string 
     */
    public function getZonename()
    {
        return $this->zonename;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return N2item
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set sortorder
     *
     * @param integer $sortorder
     * @return N2item
     */
    public function setSortorder($sortorder)
    {
        $this->sortorder = $sortorder;
    
        return $this;
    }

    /**
     * Get sortorder
     *
     * @return integer 
     */
    public function getSortorder()
    {
        return $this->sortorder;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return N2item
     */
    public function setVisible($visible)
    {
	if ($visible == "true") {
	    $visible = true;
	} elseif ($visible == "false") {
	    $visible == false;
	}
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set savedby
     *
     * @param string $savedby
     * @return N2item
     */
    public function setSavedby($savedby)
    {
        $this->savedby = $savedby;
    
        return $this;
    }

    /**
     * Get savedby
     *
     * @return string 
     */
    public function getSavedby()
    {
        return $this->savedby;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return N2item
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return integer 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set ancestraltrail
     *
     * @param string $ancestraltrail
     * @return N2item
     */
    public function setAncestraltrail($ancestraltrail)
    {
        $this->ancestraltrail = $ancestraltrail;
    
        return $this;
    }

    /**
     * Get ancestraltrail
     *
     * @return string 
     */
    public function getAncestraltrail()
    {
        return $this->ancestraltrail;
    }

    /**
     * Set versionindex
     *
     * @param integer $versionindex
     * @return N2item
     */
    public function setVersionindex($versionindex)
    {
        $this->versionindex = $versionindex;
    
        return $this;
    }

    /**
     * Get versionindex
     *
     * @return integer 
     */
    public function getVersionindex()
    {
        return $this->versionindex;
    }

    /**
     * Set alteredpermissions
     *
     * @param integer $alteredpermissions
     * @return N2item
     */
    public function setAlteredpermissions($alteredpermissions)
    {
        $this->alteredpermissions = $alteredpermissions;
    
        return $this;
    }

    /**
     * Get alteredpermissions
     *
     * @return integer 
     */
    public function getAlteredpermissions()
    {
        return $this->alteredpermissions;
    }

    /**
     * Set versionofid
     *
     * @param \Unislug\N2Bundle\Entity\N2item $versionofid
     * @return N2item
     */
    public function setVersionofid(\Unislug\N2Bundle\Entity\N2item $versionofid = null)
    {
        $this->versionofid = $versionofid;
    
        return $this;
    }

    /**
     * Get versionofid
     *
     * @return \Unislug\N2Bundle\Entity\N2item 
     */
    public function getVersionofid()
    {
        return $this->versionofid;
    }

    /**
     * Set translationofid
     *
     * @param \Unislug\N2Bundle\Entity\N2item $translationofid
     * @return N2item
     */
    public function setTranslationofid(\Unislug\N2Bundle\Entity\N2item $translationofid = null)
    {
        $this->translationofid = $translationofid;
    
        return $this;
    }

    /**
     * Get translationofid
     *
     * @return \Unislug\N2Bundle\Entity\N2item 
     */
    public function getTranslationofid()
    {
        return $this->translationofid;
    }
    
    /**
     * Set parentid
     *
     * @param \Unislug\N2Bundle\Entity\N2item $parentid
     * @return N2item
     */
    public function setParentid(\Unislug\N2Bundle\Entity\N2item $parentid = null)
    {
        $this->parentid = $parentid;
    
        return $this;
    }

    /**
     * Get parentid
     *
     * @return \Unislug\N2Bundle\Entity\N2item 
     */
    public function getParentid()
    {
        return $this->parentid;
    }

    /**
     * @ORM\OneToMany(targetEntity="N2item", mappedBy="versionofid")
     */
    private $versions;
    /**
     * @ORM\OneToMany(targetEntity="N2item", mappedBy="translationofid")
     */
    private $translations;
    /**
     * @ORM\OneToMany(targetEntity="N2item", mappedBy="parentid")
     */
    private $children;
    /**
     * @ORM\OneToMany(targetEntity="N2detail", mappedBy="itemid")
     */
    private $details;
    /**
     * @ORM\OneToMany(targetEntity="N2detailcollection", mappedBy="itemid")
     */
    private $detailcollections;
    /**
     * @ORM\OneToMany(targetEntity="N2allowedrole", mappedBy="itemid")
     */
    private $allowedroles;

    public function __construct()
    {
        $this->versions		  = new ArrayCollection();
        $this->translations	  = new ArrayCollection();
        $this->children		  = new ArrayCollection();
        $this->details 		  = new ArrayCollection();
        $this->detailcollections  = new ArrayCollection();
        $this->allowedroles 	  = new ArrayCollection();
    }

    public function getVersions() {
        return $this->versions;
    }

    public function getTranslations() {
        return $this->translations;
    }

    public function getChildren() {
        return $this->children;
    }

    public function getDetails() {
        return $this->details;
    }

    public function getDetailcollections() {
        return $this->detailcollections;
    }

    public function getAllowedroles() {
        return $this->allowedroles;
    }

    private $dmap;
    public function getd($n, $alt = null) {
        $x = $this->dmap;
        if (!isset($x)) {
            $x = array();
            foreach ($this->getDetails() as $d) {
                $x[$d->getName()] = $d;
            }
            $this->dmap = $x;
        }
        if (isset($x[$n])) {
            return $x[$n]->val();
        } else {
            return $alt;
        }
    }
    
    public function getds($keys) {
        $x = $this->dmap;
        if (!isset($x)) {
            $x = array();
            foreach ($this->getDetails() as $d) {
                $x[$d->getName()] = $d;
            };
            $this->dmap = $x;
        }
        $r = [];
        foreach ($keys as $k) {
            if (isset($x[$k])) {
                $r[] = $x[$k]->val();
            } else {
                $r[] = null;
            }
        }
        return $r;
    }

    // Dynamic object extensions
    public $view;
    public $route;

    public function asItem() { return $this; }
}