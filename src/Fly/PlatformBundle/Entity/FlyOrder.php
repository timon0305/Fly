<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\FlyOrderRepository")
 * @ORM\Table(name="flyorder", indexes={@ORM\Index(name="search_idx", columns={"created", "updated"})})
 * @ORM\HasLifecycleCallbacks()
 */
class FlyOrder implements \JsonSerializable
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
     * @ORM\Column(name="order_id", type="string")
     */
    private $order_id;

    /**
     * @var string
     * @ORM\Column(name="firstname", type="string", length=36)
     */
    private $firstname;

    /**
     * @var string
     * @ORM\Column(name="familyname", type="string", length=36)
     */
    private $familyname;

    /**
     * @var string
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @ORM\Column(name="date_of_birth", type="datetime")
     */
    private $date_of_birth;

    /**
     * @var array
     * @ORM\Column(name="outbound", type="array")
     */
    private $outbound;

    /**
     * @var array
     * @ORM\Column(name="inbound",type="array")
     */
    private $inbound;

    /**
     * @var integer
     * @ORM\Column(name="price", type="decimal", precision=7, scale=2)
     *
     */
    private $price;

    /**
     * @var string
     * @ORM\Column(name="currency", type="string")
     */
    private $currency;

    /**
     * @var boolean
     * * @ORM\Column(name="is_confirmed", type="smallint")
     */
    private $is_confirmed;

    /**
     * @var boolean
     * * @ORM\Column(name="is_payed", type="smallint")
     */
    private $is_payed;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


    /**
     * @ORM\ManyToOne(targetEntity="FlyInsurance", inversedBy="order")
     * @ORM\JoinColumn(name="insurance_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $insurance;


    public function __construct()
    {
        $this->is_payed = 0;
        $this->order_id = $this->generateOrderId();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function __toString()
    {
        return $this->id;
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
     * Set firstname
     *
     * @param string $firstname
     * @return FlyOrder
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set familyname
     *
     * @param string $familyname
     * @return FlyOrder
     */
    public function setFamilyname($familyname)
    {
        $this->familyname = $familyname;

        return $this;
    }

    /**
     * Get familyname
     *
     * @return string 
     */
    public function getFamilyname()
    {
        return $this->familyname;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return FlyOrder
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set date_of_birth
     *
     * @param \DateTime $dateOfBirth
     * @return FlyOrder
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->date_of_birth = $dateOfBirth;

        return $this;
    }

    /**
     * Get date_of_birth
     *
     * @return \DateTime 
     */
    public function getDateOfBirth()
    {
        return $this->date_of_birth;
    }

    /**
     * Set outbound
     *
     * @param array $outbound
     * @return FlyOrder
     */
    public function setOutbound($outbound)
    {
        $this->outbound = $outbound;

        return $this;
    }

    /**
     * Get outbound
     *
     * @return array 
     */
    public function getOutbound()
    {
        return $this->outbound;
    }

    /**
     * Set inbound
     *
     * @param array $inbound
     * @return FlyOrder
     */
    public function setInbound($inbound)
    {
        $this->inbound = $inbound;

        return $this;
    }

    /**
     * Get inbound
     *
     * @return array 
     */
    public function getInbound()
    {
        return $this->inbound;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return FlyOrder
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }


    /**
     * Set insurance
     *
     * @param \Fly\PlatformBundle\Entity\FlyInsurance $insurance
     * @return FlyOrder
     */
    public function setInsurance(\Fly\PlatformBundle\Entity\FlyInsurance $insurance = null)
    {
        $this->insurance = $insurance;

        return $this;
    }

    /**
     * Get insurance
     *
     * @return \Fly\PlatformBundle\Entity\FlyInsurance 
     */
    public function getInsurance()
    {
        return $this->insurance;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return FlyOrder
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set order_id
     *
     * @param string $orderId
     * @return FlyOrder
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get order_id
     *
     * @return string 
     */
    public function getOrderId()
    {
        return $this->order_id;
    }



    /**
     * Set is_payed
     *
     * @param integer $isPayed
     * @return FlyOrder
     */
    public function setIsPayed($isPayed)
    {
        $this->is_payed = $isPayed;

        return $this;
    }

    /**
     * Get is_payed
     *
     * @return integer 
     */
    public function getIsPayed()
    {
        return $this->is_payed;
    }


    protected function generateOrderId()
    {
        return strtoupper( substr( uniqid(md5(time())), 0, 10 ) );
    }


    /**
     * Set is_confirmed
     *
     * @param integer $isConfirmed
     * @return FlyOrder
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->is_confirmed = $isConfirmed;

        return $this;
    }

    /**
     * Get is_confirmed
     *
     * @return integer 
     */
    public function getIsConfirmed()
    {
        return $this->is_confirmed;
    }
}
