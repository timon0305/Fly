<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="Fly\UserBundle\Entity\GroupRepository")
 * @ORM\Table(name="fos_group")
 * @ORM\HasLifecycleCallbacks()
 */
class Group extends BaseGroup
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $picture;


    /**
     * @ORM\Column(name="is_wheretogo", type="boolean", nullable=true)
     */
    protected $is_wheretogo;

    /**
     * @ORM\Column(name="city", type="string",length=100, nullable=true)
     */
    protected $city;

    /**
     * @ORM\Column(name="geoAddress", type="string",length=100, nullable=true)
     */
    protected $geoAddress;

    /**
     * @ORM\Column(name="geoLat", type="string",length=100, nullable=true)
     */
    protected $geoLat;

    /**
     * @ORM\Column(name="geoLng", type="string",length=100, nullable=true)
     */
    protected $geoLng;

    /**
     * @ORM\Column(name="country", type="string",length=100, nullable=true)
     */
    protected $country;


    /**
     * @ORM\Column(name="is_whentogo", type="boolean", nullable=true)
     */
    protected $is_whentogo;

    /**
     * @ORM\Column(name="departure_date", type="date", nullable=true)
     */
    protected $departure_date;

    /**
     * @ORM\Column(name="departure_date_flexibilityy", type="smallint",  nullable=true)
     */
    protected $departure_date_flexibility;

    /**
     * @ORM\Column(name="wayback_date", type="date", nullable=true)
     */
    protected $wayback_date;

    /**
     * @ORM\Column(name="wayback_date_flexibility", type="smallint",  nullable=true)
     */
    protected $wayback_date_flexibility;

    /**
     * @ORM\Column(name="expired_time", type="integer", nullable=true)
     */
    protected $expired_time;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;



    /**
     * @ORM\ManyToMany(targetEntity="Fly\PlatformBundle\Entity\TravelingWith", inversedBy="groups")
     * @ORM\JoinTable(name="groups_travelingWith")
     **/
    public $travelingWith;

    /**
     * @ORM\ManyToMany(targetEntity="Fly\PlatformBundle\Entity\Spirit", inversedBy="groups")
     * @ORM\JoinTable(name="groups_spirits")
     **/
    private $spirits;

    /**
     * @ORM\ManyToMany(targetEntity="Fly\PlatformBundle\Entity\Accomodation", inversedBy="groups")
     * @ORM\JoinTable(name="groups_accomodations")
     **/
    private $accomodations;

    /**
     * @ORM\ManyToMany(targetEntity="Fly\PlatformBundle\Entity\Transportation", inversedBy="groups")
     * @ORM\JoinTable(name="groups_transportations")
     **/
    private $transportations;

    /**
     * @ORM\ManyToMany(targetEntity="Fly\PlatformBundle\Entity\Activities", inversedBy="groups")
     * @ORM\JoinTable(name="groups_activities")
     **/
    private $activities;

    /**
     * @ORM\ManyToMany(targetEntity="Fly\PlatformBundle\Entity\WorldZone", inversedBy="groups")
     * @ORM\JoinTable(name="groups_world_zoness")
     **/
    private $world_zones;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     **/
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="mygroups")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="GroupInvitation", mappedBy="group", cascade={"persist"})
     **/
    private $invitation;

    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="group", cascade={"persist","remove"})
     **/
    private $notificationGroupInvite;

    /**
     * @ORM\ManyToMany(targetEntity="Goals", inversedBy="group", cascade={"persist","remove"})
     * @ORM\JoinTable(name="groups_has_goals")
     **/
    private $goal;

    /**
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="group", cascade={"persist","remove"})
     **/
    private $feed;


    public function __construct()
    {
        parent::__construct($this->name, array());
        $this->created = new \DateTime();
        $this->travelingWith = new ArrayCollection();
        $this->spirits = new ArrayCollection();
        $this->accomodations = new ArrayCollection();
        $this->transportations = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->world_zones = new ArrayCollection();
        $this->invitation = new ArrayCollection();
        $this->goal = new ArrayCollection();
        $this->is_active = true;
    }

    public function __toString(){

        return $this->getViewName();
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
     * Get ViewName
     *
     * @return string
     */
    public function getViewName()
    {

        return $this->getName();
    }

    /**
     * Set is_active
     *
     * @param bool $is_active
     * @return Group
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;

        return $this;
    }

    /**
     * Get is_active
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set geoAddress
     *
     * @param string $geoAddress
     * @return Group
     */
    public function setGeoAddress($geoAddress)
    {
        $this->geoAddress = $geoAddress;

        return $this;
    }

    /**
     * Get geoAddress
     *
     * @return string
     */
    public function getGeoAddress()
    {
        return $this->geoAddress;
    }

    /**
     * Set geoLat
     *
     * @param string $geoLat
     * @return Group
     */
    public function setGeoLat($geoLat)
    {
        $this->geoLat = $geoLat;

        return $this;
    }

    /**
     * Get geoLat
     *
     * @return string
     */
    public function getGeoLat()
    {
        return $this->geoLat;
    }

    /**
     * Set geoLng
     *
     * @param string $geoLng
     * @return Group
     */
    public function setGeoLng($geoLng)
    {
        $this->geoLng = $geoLng;

        return $this;
    }

    /**
     * Get geoLng
     *
     * @return string
     */
    public function getGeoLng()
    {
        return $this->geoLng;
    }



    /**
     * Set city
     *
     * @param string $city
     * @return Group
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Group
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set departure_date
     *
     * @param \DateTime $departureDate
     * @return Group
     */
    public function setDepartureDate($departureDate)
    {
        $this->departure_date = $departureDate;

        return $this;
    }

    /**
     * Get departure_date
     *
     * @return \DateTime
     */
    public function getDepartureDate()
    {
        return $this->departure_date;
    }

    /**
     * Set departure_date_flexibility
     *
     * @param integer $departureDateFlexibility
     * @return Group
     */
    public function setDepartureDateFlexibility($departureDateFlexibility)
    {
        $this->departure_date_flexibility = $departureDateFlexibility;

        return $this;
    }

    /**
     * Get departure_date_flexibility
     *
     * @return integer
     */
    public function getDepartureDateFlexibility()
    {
        return $this->departure_date_flexibility;
    }

    /**
     * Set wayback_date
     *
     * @param \DateTime $waybackDate
     * @return Group
     */
    public function setWaybackDate($waybackDate)
    {
        $this->wayback_date = $waybackDate;

        return $this;
    }

    /**
     * Get wayback_date
     *
     * @return \DateTime
     */
    public function getWaybackDate()
    {
        return $this->wayback_date;
    }

    /**
     * Set wayback_date_flexibility
     *
     * @param integer $waybackDateFlexibility
     * @return Group
     */
    public function setWaybackDateFlexibility($waybackDateFlexibility)
    {
        $this->wayback_date_flexibility = $waybackDateFlexibility;

        return $this;
    }

    /**
     * Get wayback_date_flexibility
     *
     * @return integer
     */
    public function getWaybackDateFlexibility()
    {
        return $this->wayback_date_flexibility;
    }

    /**
     * Set expired_time
     *
     * @param integer $expiredTime
     * @return Group
     */
    public function setExpiredTime($expiredTime)
    {
        $this->expired_time = $expiredTime;

        return $this;
    }

    /**
     * Get expired_time
     *
     * @return integer
     */
    public function getExpiredTime()
    {
        return $this->expired_time;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Group
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
     * @return Group
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
     * @ORM\PreUpdate
     */
    public function updateUpdated()
    {
        $this->setUpdated(new \DateTime());
    }








    /**
     * Set is_wheretogo
     *
     * @param boolean $isWheretogo
     * @return Group
     */
    public function setIsWheretogo($isWheretogo)
    {
        $this->is_wheretogo = $isWheretogo;

        return $this;
    }

    /**
     * Get is_wheretogo
     *
     * @return boolean 
     */
    public function getIsWheretogo()
    {
        return $this->is_wheretogo;
    }

    /**
     * Set is_whentogo
     *
     * @param boolean $isWhentogo
     * @return Group
     */
    public function setIsWhentogo($isWhentogo)
    {
        $this->is_whentogo = $isWhentogo;

        return $this;
    }

    /**
     * Get is_whentogo
     *
     * @return boolean 
     */
    public function getIsWhentogo()
    {
        return $this->is_whentogo;
    }

    /**
     * Add users
     *
     * @param \Fly\UserBundle\Entity\User $users
     * @return Group
     */
    public function addUser(\Fly\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Fly\UserBundle\Entity\User $users
     */
    public function removeUser(\Fly\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add travelingWith
     *
     * @param \Fly\PlatformBundle\Entity\TravelingWith $travelingWith
     * @return Group
     */
    public function addTravelingWith(\Fly\PlatformBundle\Entity\TravelingWith $travelingWith)
    {
        $this->travelingWith[] = $travelingWith;

        return $this;
    }

    /**
     * Remove travelingWith
     *
     * @param \Fly\PlatformBundle\Entity\TravelingWith $travelingWith
     */
    public function removeTravelingWith(\Fly\PlatformBundle\Entity\TravelingWith $travelingWith)
    {
        $this->travelingWith->removeElement($travelingWith);
    }

    /**
     * Get travelingWith
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTravelingWith()
    {
        return $this->travelingWith;
    }


    /**
     * Add spirits
     *
     * @param \Fly\PlatformBundle\Entity\Spirit $spirits
     * @return Group
     */
    public function addSpirit(\Fly\PlatformBundle\Entity\Spirit $spirits)
    {
        $this->spirits[] = $spirits;

        return $this;
    }

    /**
     * Remove spirits
     *
     * @param \Fly\PlatformBundle\Entity\Spirit $spirits
     */
    public function removeSpirit(\Fly\PlatformBundle\Entity\Spirit $spirits)
    {
        $this->spirits->removeElement($spirits);
    }

    /**
     * Get spirits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSpirits()
    {
        return $this->spirits;
    }

    /**
     * Add accomodations
     *
     * @param \Fly\PlatformBundle\Entity\Accomodation $accomodations
     * @return Group
     */
    public function addAccomodation(\Fly\PlatformBundle\Entity\Accomodation $accomodations)
    {
        $this->accomodations[] = $accomodations;

        return $this;
    }

    /**
     * Remove accomodations
     *
     * @param \Fly\PlatformBundle\Entity\Accomodation $accomodations
     */
    public function removeAccomodation(\Fly\PlatformBundle\Entity\Accomodation $accomodations)
    {
        $this->accomodations->removeElement($accomodations);
    }

    /**
     * Get accomodations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccomodations()
    {
        return $this->accomodations;
    }

    /**
     * Add transportations
     *
     * @param \Fly\PlatformBundle\Entity\Transportation $transportations
     * @return Group
     */
    public function addTransportation(\Fly\PlatformBundle\Entity\Transportation $transportations)
    {
        $this->transportations[] = $transportations;

        return $this;
    }

    /**
     * Remove transportations
     *
     * @param \Fly\PlatformBundle\Entity\Transportation $transportations
     */
    public function removeTransportation(\Fly\PlatformBundle\Entity\Transportation $transportations)
    {
        $this->transportations->removeElement($transportations);
    }

    /**
     * Get transportations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTransportations()
    {
        return $this->transportations;
    }

    /**
     * Add activities
     *
     * @param \Fly\PlatformBundle\Entity\Activities $activities
     * @return Group
     */
    public function addActivity(\Fly\PlatformBundle\Entity\Activities $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \Fly\PlatformBundle\Entity\Activities $activities
     */
    public function removeActivity(\Fly\PlatformBundle\Entity\Activities $activities)
    {
        $this->activities->removeElement($activities);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Add world_zones
     *
     * @param \Fly\PlatformBundle\Entity\WorldZone $worldZones
     * @return Group
     */
    public function addWorldZone(\Fly\PlatformBundle\Entity\WorldZone $worldZones)
    {
        $this->world_zones[] = $worldZones;

        return $this;
    }

    /**
     * Remove world_zones
     *
     * @param \Fly\PlatformBundle\Entity\WorldZone $worldZones
     */
    public function removeWorldZone(\Fly\PlatformBundle\Entity\WorldZone $worldZones)
    {
        $this->world_zones->removeElement($worldZones);
    }

    /**
     * Get world_zones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWorldZones()
    {
        return $this->world_zones;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return Group
     */
    public function setPicture($picture)
    {
        if($picture){
            $this->picture = $picture;
        }

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
     * Add invitation
     *
     * @param \Fly\UserBundle\Entity\GroupInvitation $invitation
     * @return Group
     */
    public function addInvitation(\Fly\UserBundle\Entity\GroupInvitation $invitation)
    {
        $this->invitation[] = $invitation;

        return $this;
    }

    /**
     * Remove invitation
     *
     * @param \Fly\UserBundle\Entity\GroupInvitation $invitation
     */
    public function removeInvitation(\Fly\UserBundle\Entity\GroupInvitation $invitation)
    {
        $this->invitation->removeElement($invitation);
    }

    /**
     * Get invitation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitation()
    {
        return $this->invitation;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Group
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
     * Set owner
     *
     * @param \Fly\UserBundle\Entity\User $owner
     * @return Group
     */
    public function setOwner(\Fly\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Fly\UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }





    /**
     * Add goal
     *
     * @param \Fly\UserBundle\Entity\Goals $goal
     * @return Group
     */
    public function addGoal(\Fly\UserBundle\Entity\Goals $goal)
    {
        $this->goal[] = $goal;

        return $this;
    }

    /**
     * Remove goal
     *
     * @param \Fly\UserBundle\Entity\Goals $goal
     */
    public function removeGoal(\Fly\UserBundle\Entity\Goals $goal)
    {
        $this->goal->removeElement($goal);
    }

    /**
     * Get goal
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * Add feed
     *
     * @param \Fly\UserBundle\Entity\Feed $feed
     * @return Group
     */
    public function addFeed(\Fly\UserBundle\Entity\Feed $feed)
    {
        $this->feed[] = $feed;

        return $this;
    }

    /**
     * Remove feed
     *
     * @param \Fly\UserBundle\Entity\Feed $feed
     */
    public function removeFeed(\Fly\UserBundle\Entity\Feed $feed)
    {
        $this->feed->removeElement($feed);
    }

    /**
     * Get feed
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Add notificationGroupInvite
     *
     * @param \Fly\UserBundle\Entity\Notification $notificationGroupInvite
     * @return Group
     */
    public function addNotificationGroupInvite(\Fly\UserBundle\Entity\Notification $notificationGroupInvite)
    {
        $this->notificationGroupInvite[] = $notificationGroupInvite;

        return $this;
    }

    /**
     * Remove notificationGroupInvite
     *
     * @param \Fly\UserBundle\Entity\Notification $notificationGroupInvite
     */
    public function removeNotificationGroupInvite(\Fly\UserBundle\Entity\Notification $notificationGroupInvite)
    {
        $this->notificationGroupInvite->removeElement($notificationGroupInvite);
    }

    /**
     * Get notificationGroupInvite
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotificationGroupInvite()
    {
        return $this->notificationGroupInvite;
    }

    public function isHaveGroupInvite($user)
    {
        $inv = $this->getNotificationGroupInvite();
        foreach($inv as $i){
            if($i->getUser() == $user){
                return true;
            }
        }

//        dump($inv->count());die;
        return false;
//        dump($inv->count());die;
    }
}
