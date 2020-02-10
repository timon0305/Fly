<?php

namespace Fly\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Fly\UserBundle\Entity\UserRepository")
 * @ORM\Table("fos_user")
 *
 */
class User extends BaseUser implements \JsonSerializable
{



    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
        $this->invitation = new ArrayCollection();
    }
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $first_name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $last_name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;


    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $gender;

    /**
     * @var string
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $mstatus;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $hometown;


    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $location;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $hometownFb;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $locationFb;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $locationTw;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $mobilePhone;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $photo;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $photo_small;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $cover;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;


    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="owner", cascade={"persist","remove"})
     **/
    private $mygroups;

    /**
     * @ORM\OneToMany(targetEntity="GroupInvitation", mappedBy="user", cascade={"persist"})
     **/
    protected $invitation;

    /**
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="user", cascade={"persist","remove"})
     **/
    private $feed;

    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user", cascade={"persist","remove"})
     **/
    private $notification;

    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="sender", cascade={"persist","remove"})
     **/
    private $notificationMine;

    /**
     * @ORM\OneToMany(targetEntity="FeedComment", mappedBy="user", cascade={"persist","remove"})
     **/
    private $comment;

    /**
     * @ORM\OneToMany(targetEntity="FeedLike", mappedBy="user", cascade={"persist","remove"})
     **/
    private $like;

    /**
     * @ORM\ManyToMany(targetEntity="Feed", mappedBy="userViews", cascade={"persist"})
     **/
    private $feedViews;

    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $facebookId;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $facebookUserImage;

    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $twitterId;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $twitterUserImage;

    /**
     * @var string
     */
    private $googleId;

    
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
     * Set first_name
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    public function __toString()
    {
        if($this->getFirstName() || $this->getLastName()){
            return $this->getFirstName().' '.$this->getLastName();
        }else{
            return $this->getUsername();
        }

    }





    /**
     * Add groups
     *
     * @param \Fly\UserBundle\Entity\Group $groups
     * @return User
     */
    public function addGroup(\FOS\UserBundle\Model\GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Fly\UserBundle\Entity\Group $groups
     */
    public function removeGroup(\FOS\UserBundle\Model\GroupInterface $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto()
    {
        return $this->photo;
    }


    /**
     * Set photo_small
     *
     * @param string $photo_small
     * @return User
     */
    public function setPhotoSmall($photo_small)
    {
        $this->photo_small = $photo_small;

        return $this;
    }

    /**
     * Get photo_small
     *
     * @return string
     */
    public function getPhotoSmall()
    {
        return $this->photo_small;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return User
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set hometown
     *
     * @param string $hometown
     * @return User
     */
    public function setHometown($hometown)
    {
        $this->hometown = $hometown;

        return $this;
    }

    /**
     * Get hometown
     *
     * @return string 
     */
    public function getHometown()
    {
//        if(!$this->hometown){
//            if($this->getHometownFb()){
//                return $this->getHometownFb();
//            }
//        }
        return $this->hometown;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return User
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set hometownFb
     *
     * @param string $hometownFb
     * @return User
     */
    public function setHometownFb($hometownFb)
    {
        $this->hometownFb = $hometownFb;

        return $this;
    }

    /**
     * Get hometownFb
     *
     * @return string
     */
    public function getHometownFb()
    {
        return $this->hometownFb;
    }

    /**
     * Set locationFb
     *
     * @param string $locationFb
     * @return User
     */
    public function setLocationFb($locationFb)
    {
        $this->locationFb = $locationFb;

        return $this;
    }

    /**
     * Get locationFb
     *
     * @return string
     */
    public function getLocationFb()
    {
        return $this->locationFb;
    }

    /**
     * Set locationTw
     *
     * @param string $locationTw
     * @return User
     */
    public function setLocationTw($locationTw)
    {
        $this->locationTw = $locationTw;

        return $this;
    }

    /**
     * Get locationTw
     *
     * @return string
     */
    public function getLocationTw()
    {
        return $this->locationTw;
    }

    /**
     * Set mobilePhone
     *
     * @param string $mobilePhone
     * @return User
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * Get mobilePhone
     *
     * @return string 
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * Set mstatus
     *
     * @param string $mstatus
     * @return User
     */
    public function setMstatus($mstatus)
    {
        $this->mstatus = $mstatus;

        return $this;
    }

    /**
     * Get mstatus
     *
     * @return string 
     */
    public function getMstatus()
    {
        return $this->mstatus;
    }

    /**
     * Add invitation
     *
     * @param \Fly\UserBundle\Entity\GroupInvitation $invitation
     * @return User
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
     * Add mygroups
     *
     * @param \Fly\UserBundle\Entity\Group $mygroups
     * @return User
     */
    public function addMygroup(\Fly\UserBundle\Entity\Group $mygroups)
    {
        $this->mygroups[] = $mygroups;

        return $this;
    }

    /**
     * Remove mygroups
     *
     * @param \Fly\UserBundle\Entity\Group $mygroups
     */
    public function removeMygroup(\Fly\UserBundle\Entity\Group $mygroups)
    {
        $this->mygroups->removeElement($mygroups);
    }

    /**
     * Get mygroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMygroups()
    {
        return $this->mygroups;
    }

    /**
     * Set cover
     *
     * @param string $cover
     * @return User
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * Get cover
     *
     * @return string 
     */
    public function getCover()
    {
        if($this->cover){

        }
        return $this->cover;
    }

    /**
     * Add feed
     *
     * @param \Fly\UserBundle\Entity\Feed $feed
     * @return User
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
     * Add comment
     *
     * @param \Fly\UserBundle\Entity\FeedComment $comment
     * @return User
     */
    public function addComment(\Fly\UserBundle\Entity\FeedComment $comment)
    {
        $this->comment[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \Fly\UserBundle\Entity\FeedComment $comment
     */
    public function removeComment(\Fly\UserBundle\Entity\FeedComment $comment)
    {
        $this->comment->removeElement($comment);
    }

    /**
     * Get comment
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function imageUploadsDir()
    {
        return __DIR__ . '/../../../../../web/uploads';
    }

    public function imageWebDir()
    {
        return '/uploads';
    }

    public function WebDir()
    {
        return '/';
    }

    public function getProfileImage()
    {
        return $this->imageUploadsDir().'/'.$this->username.'/profile/'.$this->getPhoto();
    }

    public function getProfileImageSm()
    {
        return $this->imageUploadsDir().'/'.$this->username.'/profile/profile-picture_sm.jpg';
    }

    public function jsonSerialize()
    {
        $photo = null;
        $photo_sm = null;

        if(!$this->getFacebookId() || !$this->getTwitterId()){
            $userphoto = $this->photo;
            $arr = explode('.',$this->photo);
            $ext = end($arr);
            $photo = '/uploads/'.$this->photo;
            $photo_sm = $this->imageWebDir().'/'.$this->username.'/profile/profile-picture_sm.'.$ext;
        }

        if($this->getFacebookId()){
            $photo = $this->getFacebookUserImage();
            $photo_sm = $this->getFacebookUserImage();
        }

        if($this->getTwitterId()){
            $photo = $this->getTwitterUserImage();
            $photo_sm = $this->getTwitterUserImage();
        }





        return [
            'id'=>$this->id,
            'username'=>$this->username,
            'email'=>$this->email,
            'name'=>$this->getFirstName() . ' ' . $this->getLastName(),
            'photo'=>$this->getUserProfileImage(),
            'photo_sm'=>$photo_sm,
            'user_cover'=>$this->getUserCoverImage(),
        ];
    }

    /**
     * Add like
     *
     * @param \Fly\UserBundle\Entity\FeedLike $like
     * @return User
     */
    public function addLike(\Fly\UserBundle\Entity\FeedLike $like)
    {
        $this->like[] = $like;

        return $this;
    }

    /**
     * Remove like
     *
     * @param \Fly\UserBundle\Entity\FeedLike $like
     */
    public function removeLike(\Fly\UserBundle\Entity\FeedLike $like)
    {
        $this->like->removeElement($like);
    }

    /**
     * Get like
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLike()
    {
        return $this->like;
    }

    /**
     * Add feedViews
     *
     * @param \Fly\UserBundle\Entity\Feed $feedViews
     * @return User
     */
    public function addFeedView(\Fly\UserBundle\Entity\Feed $feedViews)
    {
        $this->feedViews[] = $feedViews;

        return $this;
    }

    /**
     * Remove feedViews
     *
     * @param \Fly\UserBundle\Entity\Feed $feedViews
     */
    public function removeFeedView(\Fly\UserBundle\Entity\Feed $feedViews)
    {
        $this->feedViews->removeElement($feedViews);
    }

    /**
     * Get feedViews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeedViews()
    {
        return $this->feedViews;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set facebookUserImage
     *
     * @param string $url
     * @return User
     */
    public function setFacebookUserImage($url)
    {
        $this->facebookUserImage = $url;

        return $this;
    }

    /**
     * Get facebookUserImage
     *
     * @return string
     */
    public function getFacebookUserImage()
    {
        return $this->facebookUserImage;
    }

    /**
     * Set twitterId
     *
     * @param string $twitterId
     * @return User
     */
    public function setTwitterId($twitterId)
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    /**
     * Get twitterId
     *
     * @return string 
     */
    public function getTwitterId()
    {
        return $this->twitterId;
    }

    /**
     * Set twitterUserImage
     *
     * @param string $url
     * @return User
     */
    public function setTwitterUserImage($url)
    {
        $this->twitterUserImage = $url;

        return $this;
    }

    /**
     * Get twitterUserImage
     *
     * @return string
     */
    public function getTwitterUserImage()
    {
        return $this->twitterUserImage;
    }

    /**
     * Set googleId
     *
     * @param string $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * Get googleId
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    public function getUserHometown()
    {
        if($this->getHometown()){
            return $this->getHometown();
        }

        if($this->getFacebookId()){
            return $this->getHometownFb();
        }

        return null;
    }

    public function getUserCurrentCity()
    {
        if($this->getLocation()){
            return $this->getLocation();
        }
        if($this->getFacebookId()){
            return $this->getLocationFb();
        }
        if($this->getTwitterId()){
            return $this->getLocationTw();
        }

        return null;
    }

    public function getUserProfileImage()
    {
        $photo = '/no-user-photo.jpg';
//        $photo_sm = null;

        if(!$this->getFacebookId() || !$this->getTwitterId()){
            if($this->photo){
                $photo = $this->imageWebDir().'/'.$this->photo;
            }

        }

        if($this->getFacebookId()){
            $photo = $this->getFacebookUserImage();
        }

        if($this->getTwitterId()){
            $photo = $this->getTwitterUserImage();
        }

        return $photo;
    }

    public function getUserCoverImage()
    {
        $photo = '/no-user-cover.png';

        if($this->cover){
           $photo = $this->imageWebDir().'/'.$this->cover;
        }

        return $photo;
    }

    public function isGroupMember($group){
        return $this->groups->contains($group);
    }



    public function isGroupOwner($group){

        return $this == $group->getOwner();
    }

    /**
     * Add notification
     *
     * @param \Fly\UserBundle\Entity\Notification $notification
     * @return User
     */
    public function addNotification(\Fly\UserBundle\Entity\Notification $notification)
    {
        $this->notification[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \Fly\UserBundle\Entity\Notification $notification
     */
    public function removeNotification(\Fly\UserBundle\Entity\Notification $notification)
    {
        $this->notification->removeElement($notification);
    }

    /**
     * Get notification
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotification()
    {
        return $this->notification;
    }



    /**
     * Add notificationMine
     *
     * @param \Fly\UserBundle\Entity\Notification $notificationMine
     * @return User
     */
    public function addNotificationMine(\Fly\UserBundle\Entity\Notification $notificationMine)
    {
        $this->notificationMine[] = $notificationMine;

        return $this;
    }

    /**
     * Remove notificationMine
     *
     * @param \Fly\UserBundle\Entity\Notification $notificationMine
     */
    public function removeNotificationMine(\Fly\UserBundle\Entity\Notification $notificationMine)
    {
        $this->notificationMine->removeElement($notificationMine);
    }

    /**
     * Get notificationMine
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotificationMine()
    {
        return $this->notificationMine;
    }
}
