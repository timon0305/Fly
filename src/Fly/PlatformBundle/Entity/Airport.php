<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\AirportRepository")
 * @ORM\Table(name="airport", indexes={@ORM\Index(name="search_idx", columns={"city_name", "airport_code", "country_name"})})
 * @ORM\HasLifecycleCallbacks()
 */
class Airport implements \JsonSerializable
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
     * @ORM\Column(name="city_name", type="string", length=30)
     */
    private $city_name;

    /**
     * @var string
     * @ORM\Column(name="city_code", type="string", length=5)
     */
    private $city_code;

    /**
     * @var string
     * @ORM\Column(name="airport_code",type="string", length=5, nullable=false)
     */
    private $airport_code;

    /**
     * @var string
     * @ORM\Column(name="airport_name",type="string", length=30, nullable=false)
     */
    private $airport_name;

    /**
     * @var string
     * @ORM\Column(name="country_name",type="string", length=30, nullable=true)
     */
    private $country_name;

    /**
     * @var string
     * @ORM\Column(name="country_abbrev",type="string", length=5, nullable=true)
     */
    private $country_abbrev;

    /**
     * @var string
     * @ORM\Column(name="world_area_code",type="string", length=5, nullable=true)
     */
    private $world_area_code;



//    public function __construct()
//    {
//        $this->created = new \DateTime();
//        $this->updated = new \DateTime();
//    }

    public function __toString()
    {
        return $this->airport_name;
    }

    public function jsonSerialize()
    {
        return [
            'id'=>$this->id,
            'city_name'=>$this->city_name,
            'airport_code'=>$this->airport_code,
            'airport_name'=>$this->airport_name,
            'country_name'=>$this->country_name,
            'country_abbrev'=>$this->country_abbrev,
            'world_area_code'=>$this->world_area_code,
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
     * Set city_name
     *
     * @param string $cityName
     * @return Airport
     */
    public function setCityName($cityName)
    {
        $this->city_name = $cityName;

        return $this;
    }

    /**
     * Get city_name
     *
     * @return string 
     */
    public function getCityName()
    {
        return $this->city_name;
    }

    /**
     * Set airport_code
     *
     * @param string $airportCode
     * @return Airport
     */
    public function setAirportCode($airportCode)
    {
        $this->airport_code = $airportCode;

        return $this;
    }

    /**
     * Get airport_code
     *
     * @return string 
     */
    public function getAirportCode()
    {
        return $this->airport_code;
    }

    /**
     * Set airport_name
     *
     * @param string $airportName
     * @return Airport
     */
    public function setAirportName($airportName)
    {
        $this->airport_name = $airportName;

        return $this;
    }

    /**
     * Get airport_name
     *
     * @return string 
     */
    public function getAirportName()
    {
        return $this->airport_name;
    }

    /**
     * Set country_name
     *
     * @param string $countryName
     * @return Airport
     */
    public function setCountryName($countryName)
    {
        $this->country_name = $countryName;

        return $this;
    }

    /**
     * Get country_name
     *
     * @return string 
     */
    public function getCountryName()
    {
        return $this->country_name;
    }

    /**
     * Set country_abbrev
     *
     * @param string $countryAbbrev
     * @return Airport
     */
    public function setCountryAbbrev($countryAbbrev)
    {
        $this->country_abbrev = $countryAbbrev;

        return $this;
    }

    /**
     * Get country_abbrev
     *
     * @return string 
     */
    public function getCountryAbbrev()
    {
        return $this->country_abbrev;
    }

    /**
     * Set world_area_code
     *
     * @param string $worldAreaCode
     * @return Airport
     */
    public function setWorldAreaCode($worldAreaCode)
    {
        $this->world_area_code = $worldAreaCode;

        return $this;
    }

    /**
     * Get world_area_code
     *
     * @return string 
     */
    public function getWorldAreaCode()
    {
        return $this->world_area_code;
    }

    /**
     * Set city_code
     *
     * @param string $cityCode
     * @return Airport
     */
    public function setCityCode($cityCode)
    {
        $this->city_code = $cityCode;

        return $this;
    }

    /**
     * Get city_code
     *
     * @return string 
     */
    public function getCityCode()
    {
        return $this->city_code;
    }
}
