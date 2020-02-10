<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\ActRepository")
 * @ORM\Table(name="act")
 * @ORM\HasLifecycleCallbacks()
 */
class Act implements \JsonSerializable
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
     * @ORM\Column(type="string")
     */
    private $name;


    /**
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(name="currency", type="string")
     */
    private $currency;


    /**
     * @ORM\Column(name="address",type="text")
     */
    private $address;

    /**
     * @ORM\Column(name="lat",type="text")
     */
    private $lat;

    /**
     * @ORM\Column(name="lng",type="text")
     */
    private $lng;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;


    /**
     * @ORM\ManyToOne(targetEntity="ActCategory", inversedBy="act")
     * @ORM\JoinColumn(name="act_category_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $actCategory;

    /**
     * @ORM\OneToMany(targetEntity="ActItem", mappedBy="act", cascade={"persist","remove"})
     **/
    private $actItem;



    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->actItem = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function jsonSerialize()
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'price'=>$this->price,
            'currency'=>$this->currency,
            'address'=>$this->address,
            'lat'=>$this->lat,
            'lng'=>$this->lng,
            'category'=>$this->actCategory,
//            'items'=>$this->actItem
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
     * Set name
     *
     * @param string $name
     * @return Act
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
     * Set price
     *
     * @param string $price
     * @return Act
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
     * Set currency
     *
     * @param string $currency
     * @return Act
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
     * Set address
     *
     * @param string $address
     * @return Act
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set lat
     *
     * @param string $lat
     * @return Act
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     * @return Act
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return string 
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Act
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
     * @return Act
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
     * Set actCategory
     *
     * @param \Fly\PlatformBundle\Entity\ActCategory $actCategory
     * @return Act
     */
    public function setActCategory(\Fly\PlatformBundle\Entity\ActCategory $actCategory = null)
    {
        $this->actCategory = $actCategory;

        return $this;
    }

    /**
     * Get actCategory
     *
     * @return \Fly\PlatformBundle\Entity\ActCategory 
     */
    public function getActCategory()
    {
        return $this->actCategory;
    }



    /**
     * Add actItem
     *
     * @param \Fly\PlatformBundle\Entity\ActItem $actItem
     * @return Act
     */
    public function addActItem(\Fly\PlatformBundle\Entity\ActItem $actItem)
    {
        $this->actItem[] = $actItem;

        return $this;
    }

    /**
     * Remove actItem
     *
     * @param \Fly\PlatformBundle\Entity\ActItem $actItem
     */
    public function removeActItem(\Fly\PlatformBundle\Entity\ActItem $actItem)
    {
        $this->actItem->removeElement($actItem);
    }

    /**
     * Get actItem
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActItem()
    {
        return $this->actItem;
    }
}
