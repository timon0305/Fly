<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\FlyCacheRepository")
 * @ORM\Table(name="flycache", indexes={@ORM\Index(name="search_idx", columns={"airport_one", "airport_two", "created"})})
 * @ORM\HasLifecycleCallbacks()
 */
class FlyCache implements \JsonSerializable
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
     * @ORM\Column(name="airport_one",type="string", length=5)
     */
    private $airport_one;

    /**
     * @var string
     * @ORM\Column(name="airport_two",type="string", length=5)
     */
    private $airport_two;

    /**
     * @ORM\Column(name="departure_out", type="datetime")
     */
    private $departure_out;

    /**
     * @ORM\Column(name="departure_in", type="datetime")
     */
    private $departure_in;

    /**
     * @var array
     * @ORM\Column(name="recommendation", type="array")
     */
    private $recommendation;

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
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


    public function __construct()
    {
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
     * Set airport_one
     *
     * @param string $airportOne
     * @return FlyCache
     */
    public function setAirportOne($airportOne)
    {
        $this->airport_one = $airportOne;

        return $this;
    }

    /**
     * Get airport_one
     *
     * @return string 
     */
    public function getAirportOne()
    {
        return $this->airport_one;
    }

    /**
     * Set airport_two
     *
     * @param string $airportTwo
     * @return FlyCache
     */
    public function setAirportTwo($airportTwo)
    {
        $this->airport_two = $airportTwo;

        return $this;
    }

    /**
     * Get airport_two
     *
     * @return string 
     */
    public function getAirportTwo()
    {
        return $this->airport_two;
    }

    /**
     * Set departure_out
     *
     * @param \DateTime $departureOut
     * @return FlyCache
     */
    public function setDepartureOut($departureOut)
    {
        $this->departure_out = $departureOut;

        return $this;
    }

    /**
     * Get departure_out
     *
     * @return \DateTime 
     */
    public function getDepartureOut()
    {
        return $this->departure_out;
    }

    /**
     * Set departure_in
     *
     * @param \DateTime $departureIn
     * @return FlyCache
     */
    public function setDepartureIn($departureIn)
    {
        $this->departure_in = $departureIn;

        return $this;
    }

    /**
     * Get departure_in
     *
     * @return \DateTime 
     */
    public function getDepartureIn()
    {
        return $this->departure_in;
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
     * Set recommendation
     *
     * @param array $recommendation
     * @return FlyCache
     */
    public function setRecommendation($recommendation)
    {
        $this->recommendation = $recommendation;

        return $this;
    }

    /**
     * Get recommendation
     *
     * @return array 
     */
    public function getRecommendation()
    {
        return $this->recommendation;
    }

    /**
     * Set outbound
     *
     * @param array $outbound
     * @return FlyCache
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
     * @return FlyCache
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
}
