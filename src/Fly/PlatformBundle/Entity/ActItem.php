<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\ActItemRepository")
 * @ORM\Table(name="act_item")
 * @ORM\HasLifecycleCallbacks()
 */
class ActItem implements \JsonSerializable
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
     * @ORM\Column(name="checkin", type="datetime")
     */
    protected $checkin;

    /**
     * @ORM\Column(name="checkout", type="datetime", nullable=true)
     */
    protected $checkout;

    /**
     * @ORM\Column(name="duration", type="integer", nullable=true)
     */
    protected $duration;


    /**
     * @ORM\ManyToOne(targetEntity="Act", inversedBy="actItem")
     * @ORM\JoinColumn(name="act_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $act;





    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function __toString()
    {
        return '';//$this->checkin;
    }



    public function jsonSerialize()
    {
        return [
            'id'=>$this->id,
            'type'=>'actItem',
            'checkin'=>$this->checkin,
            'checkout'=>$this->checkout,
            'duration'=>$this->duration,
            'act'=>$this->getAct()
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
     * Set checkin
     *
     * @param \DateTime $checkin
     * @return ActItem
     */
    public function setCheckin($checkin)
    {
        $this->checkin = $checkin;

        return $this;
    }

    /**
     * Get checkin
     *
     * @return \DateTime 
     */
    public function getCheckin()
    {
        return $this->checkin;
    }

    /**
     * Set checkout
     *
     * @param \DateTime $checkout
     * @return ActItem
     */
    public function setCheckout($checkout)
    {
        $this->checkout = $checkout;

        return $this;
    }

    /**
     * Get checkout
     *
     * @return \DateTime 
     */
    public function getCheckout()
    {
        return $this->checkout;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return ActItem
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }



    /**
     * Set act
     *
     * @param \Fly\PlatformBundle\Entity\Act $act
     * @return ActItem
     */
    public function setAct(\Fly\PlatformBundle\Entity\Act $act = null)
    {
        $this->act = $act;

        return $this;
    }

    /**
     * Get act
     *
     * @return \Fly\PlatformBundle\Entity\Act 
     */
    public function getAct()
    {
        return $this->act;
    }
}
