<?php

namespace Unislug\N2Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * N2detailcollection
 *
 * @ORM\Table(name="n2DetailCollection")
 * @ORM\Entity
 */
class N2detailcollection
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
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var \N2item
     *
     * @ORM\ManyToOne(targetEntity="N2item", inversedBy="detailcollections")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="itemid", referencedColumnName="id")
     * })
     */
    private $itemid;



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
     * Set name
     *
     * @param string $name
     * @return N2detailcollection
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
     * Set itemid
     *
     * @param \Unislug\N2Bundle\Entity\N2item $itemid
     * @return N2detailcollection
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
     * @ORM\OneToMany(targetEntity="N2detail", mappedBy="detailcollectionid")
     */
    private $details;
    
    public function __construct()
    {
        $this->details 			  = new ArrayCollection();
    }

    public function getDetails() {
        return $this->details;
    }
}