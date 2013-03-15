<?php

namespace Unislug\N2Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * N2allowedrole
 *
 * @ORM\Table(name="n2AllowedRole")
 * @ORM\Entity
 */
class N2allowedrole
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
     * @ORM\Column(name="role", type="string", length=50, nullable=false)
     */
    private $role;

    /**
     * @var \N2item
     *
     * @ORM\ManyToOne(targetEntity="N2item", inversedBy="allowedroles")
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
     * Set role
     *
     * @param string $role
     * @return N2allowedrole
     */
    public function setRole($role)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set itemid
     *
     * @param \Unislug\N2Bundle\Entity\N2item $itemid
     * @return N2allowedrole
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
}