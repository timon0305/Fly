<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\FlyInsuranceOrderRepository")
 * @ORM\Table(name="flyinsurance")
 * @ORM\HasLifecycleCallbacks()
 */
class FlyInsurance implements \JsonSerializable
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     */
    private $name;

    /**
     * @var integer
     * @ORM\Column(name="insurance", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $insurance;


    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


    /**
     * @ORM\OneToMany(targetEntity="FlyOrder", mappedBy="insurance", cascade={"persist","remove"})
     **/
    private $order;



    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function __toString()
    {
        return (string)$this->name;
    }

    public function jsonSerialize()
    {
        return [
            'id'=>$this->id,

        ];
    }





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
     * Set created
     *
     * @param \DateTime $created
     * @return FlyCache
     */
    public function setCreated($created)
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return FlyCache
     */
    public function setUpdated($updated)
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
     * Set name
     *
     * @param string $name
     * @return FlyInsurance
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
     * Set insurance
     *
     * @param integer $insurance
     * @return FlyInsurance
     */
    public function setInsurance($insurance)
    {
        $this->insurance = $insurance;

        return $this;
    }

    /**
     * Get insurance
     *
     * @return integer 
     */
    public function getInsurance()
    {
        return $this->insurance;
    }

    /**
     * Add order
     *
     * @param \Fly\PlatformBundle\Entity\FlyOrder $order
     * @return FlyInsurance
     */
    public function addOrder(\Fly\PlatformBundle\Entity\FlyOrder $order)
    {
        $this->order[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \Fly\PlatformBundle\Entity\FlyOrder $order
     */
    public function removeOrder(\Fly\PlatformBundle\Entity\FlyOrder $order)
    {
        $this->order->removeElement($order);
    }

    /**
     * Get order
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
