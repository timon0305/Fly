<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\AccItemRepository")
 * @ORM\Table(name="acc_item")
 * @ORM\HasLifecycleCallbacks()
 */
class AccItem implements \JsonSerializable
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
     * @ORM\ManyToOne(targetEntity="Acc", inversedBy="accItem")
     * @ORM\JoinColumn(name="acc_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $acc;





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
            'type'=>'accItem',
            'checkin'=>$this->checkin,
            'checkout'=>$this->checkout,
            'duration'=>$this->duration,
            'acc'=>$this->getAcc()
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
     * @return AccItem
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
     * @return AccItem
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
     * @return AccItem
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
     * Set acc
     *
     * @param \Fly\PlatformBundle\Entity\Acc $acc
     * @return AccItem
     */
    public function setAcc(\Fly\PlatformBundle\Entity\Acc $acc = null)
    {
        $this->acc = $acc;

        return $this;
    }

    /**
     * Get acc
     *
     * @return \Fly\PlatformBundle\Entity\Acc 
     */
    public function getAcc()
    {
        return $this->acc;
    }
}
