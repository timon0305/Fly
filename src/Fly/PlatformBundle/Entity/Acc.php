<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\AccRepository")
 * @ORM\Table(name="acc")
 * @ORM\HasLifecycleCallbacks()
 */
class Acc implements \JsonSerializable
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
     * @ORM\Column(name="description",type="text")
     */
    private $description;

    /**
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(name="currency", type="string")
     */
    private $currency;


    /**
     * @var string
     * @ORM\Column(name="picture", type="string", nullable=true)
     */
    private $picture;


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
     * @ORM\ManyToOne(targetEntity="AccCategory", inversedBy="acc")
     * @ORM\JoinColumn(name="acc_category_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $accCategory;

    /**
     * @ORM\OneToMany(targetEntity="AccItem", mappedBy="acc", cascade={"persist","remove"})
     **/
    private $accItem;

//    /**
//     * @ORM\ManyToMany(targetEntity="AccCategory", inversedBy="acc")
//     * @ORM\JoinTable(name="acc_has_category")
//     **/
//    public $accCategory;


//    /**
//     * @ORM\ManyToMany(targetEntity="AccCategory", mappedBy="acc")
//     **/
//    public $accCategory;


    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
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
            'description'=>$this->description,
            'price'=>$this->price,
            'currency'=>$this->currency,
            'picture'=>$this->picture,
            'address'=>$this->address,
            'lat'=>$this->lat,
            'lng'=>$this->lng,
            'category'=>$this->accCategory
//            'created_timastamp'=>$this->getCreated()->getTimestamp(),
//            'updated_timestamp'=>$this->getCreated()->getTimestamp(),
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
     * @return Acc
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
     * Set description
     *
     * @param string $description
     * @return Acc
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return Acc
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
     * @return Acc
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
     * Set picture
     *
     * @param string $picture
     * @return Acc
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string 
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Acc
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
     * @return Acc
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
     * @return Acc
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
     * @return Acc
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
     * @return Acc
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
     * Set accCategory
     *
     * @param \Fly\PlatformBundle\Entity\AccCategory $accCategory
     * @return Acc
     */
    public function setAccCategory(\Fly\PlatformBundle\Entity\AccCategory $accCategory = null)
    {
        $this->accCategory = $accCategory;

        return $this;
    }

    /**
     * Get accCategory
     *
     * @return \Fly\PlatformBundle\Entity\AccCategory 
     */
    public function getAccCategory()
    {
        return $this->accCategory;
    }

    /**
     * Add accItem
     *
     * @param \Fly\PlatformBundle\Entity\AccItem $accItem
     * @return Acc
     */
    public function addAccItem(\Fly\PlatformBundle\Entity\AccItem $accItem)
    {
        $this->accItem[] = $accItem;

        return $this;
    }

    /**
     * Remove accItem
     *
     * @param \Fly\PlatformBundle\Entity\AccItem $accItem
     */
    public function removeAccItem(\Fly\PlatformBundle\Entity\AccItem $accItem)
    {
        $this->accItem->removeElement($accItem);
    }

    /**
     * Get accItem
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccItem()
    {
        return $this->accItem;
    }
}
