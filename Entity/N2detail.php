<?php

namespace Unislug\N2Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * N2detail
 *
 * @ORM\Table(name="n2Detail")
 * @ORM\Entity
 */
class N2detail
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolvalue", type="boolean", nullable=true)
     */
    private $boolvalue;

    /**
     * @var integer
     *
     * @ORM\Column(name="intvalue", type="integer", nullable=true)
     */
    private $intvalue;

    /**
     * @var float
     *
     * @ORM\Column(name="doublevalue", type="float", nullable=true)
     */
    private $doublevalue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetimevalue", type="datetime", nullable=true)
     */
    private $datetimevalue;

    /**
     * @var string
     *
     * @ORM\Column(name="stringvalue", type="text", nullable=true)
     */
    private $stringvalue;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="blob", nullable=true)
     */
    private $value;

    /**
     * @var \N2item
     *
     * @ORM\ManyToOne(targetEntity="N2item")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="linkvalue", referencedColumnName="id")
     * })
     */
    private $linkvalue;

    /**
     * @var \N2item
     *
     * @ORM\ManyToOne(targetEntity="N2item", inversedBy="details")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="itemid", referencedColumnName="id")
     * })
     */
    private $itemid;

    /**
     * @var \N2detailcollection
     *
     * @ORM\ManyToOne(targetEntity="N2detailcollection", inversedBy="details")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="detailcollectionid", referencedColumnName="id")
     * })
     */
    private $detailcollectionid;



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
     * @return N2detail
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
     * Set name
     *
     * @param string $name
     * @return N2detail
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
     * Set boolvalue
     *
     * @param boolean $boolvalue
     * @return N2detail
     */
    public function setBoolvalue($boolvalue)
    {
	if ($boolvalue == "true") {
	    $boolvalue = true;
	} elseif ($boolvalue == "false") {
	    $boolvalue == false;
	}

        $this->boolvalue = $boolvalue;
    
        return $this;
    }

    /**
     * Get boolvalue
     *
     * @return boolean 
     */
    public function getBoolvalue()
    {
        return $this->boolvalue;
    }

    /**
     * Set intvalue
     *
     * @param integer $intvalue
     * @return N2detail
     */
    public function setIntvalue($intvalue)
    {
        $this->intvalue = $intvalue;
    
        return $this;
    }

    /**
     * Get intvalue
     *
     * @return integer 
     */
    public function getIntvalue()
    {
        return $this->intvalue;
    }

    /**
     * Set doublevalue
     *
     * @param float $doublevalue
     * @return N2detail
     */
    public function setDoublevalue($doublevalue)
    {
        $this->doublevalue = $doublevalue;
    
        return $this;
    }

    /**
     * Get doublevalue
     *
     * @return float 
     */
    public function getDoublevalue()
    {
        return $this->doublevalue;
    }

    /**
     * Set datetimevalue
     *
     * @param \DateTime $datetimevalue
     * @return N2detail
     */
    public function setDatetimevalue(\DateTime $datetimevalue)
    {
        $this->datetimevalue = $datetimevalue;
    
        return $this;
    }

    /**
     * Get datetimevalue
     *
     * @return \DateTime 
     */
    public function getDatetimevalue()
    {
        return $this->datetimevalue;
    }

    /**
     * Set stringvalue
     *
     * @param string $stringvalue
     * @return N2detail
     */
    public function setStringvalue($stringvalue)
    {
        $this->stringvalue = $stringvalue;
    
        return $this;
    }

    /**
     * Get stringvalue
     *
     * @return string 
     */
    public function getStringvalue()
    {
        return $this->stringvalue;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return N2detail
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set linkvalue
     *
     * @param \Unislug\N2Bundle\Entity\N2item $linkvalue
     * @return N2detail
     */
    public function setLinkvalue(\Unislug\N2Bundle\Entity\N2item $linkvalue = null)
    {
        $this->linkvalue = $linkvalue;
    
        return $this;
    }

    /**
     * Get linkvalue
     *
     * @return \Unislug\N2Bundle\Entity\N2item 
     */
    public function getLinkvalue()
    {
        return $this->linkvalue;
    }

    /**
     * Set itemid
     *
     * @param \Unislug\N2Bundle\Entity\N2item $itemid
     * @return N2detail
     */
    public function setItemid(\Unislug\N2Bundle\Entity\N2item $itemid = null)
    {
        $this->itemid = $itemid;
    
        return $this;
    }

    /**
     * Get itemid
     *
     * @return \Unislug\N2Bundle\Entity\N2item 
     */
    public function getItemid()
    {
        return $this->itemid;
    }

    /**
     * Set detailcollectionid
     *
     * @param \Unislug\N2Bundle\Entity\N2detailcollection $detailcollectionid
     * @return N2detail
     */
    public function setDetailcollectionid(\Unislug\N2Bundle\Entity\N2detailcollection $detailcollectionid = null)
    {
        $this->detailcollectionid = $detailcollectionid;
    
        return $this;
    }

    /**
     * Get detailcollectionid
     *
     * @return \Unislug\N2Bundle\Entity\N2detailcollection 
     */
    public function getDetailcollectionid()
    {
        return $this->detailcollectionid;
    }

    public function val()
    {
        $x = $this->stringvalue;
        if (isset($x)) return $x;
        $x = $this->intvalue;
        if (isset($x)) return $x;
        $x = $this->boolvalue;
        if (isset($x)) return $x?1:0;
        $x = $this->datetimevalue;
        if (isset($x)) return $x;
        $x = $this->doublevalue;
        if (isset($x)) return $x;
        $x = $this->linkvalue;
        if (isset($x)) return $x;
        $x = $this->value;
        if (isset($x)) return $x;
    }
}