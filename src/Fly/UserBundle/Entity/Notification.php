<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Fly\UserBundle\FlyUserBundle;


/**
 * @ORM\Entity(repositoryClass="Fly\UserBundle\Entity\NotificationRepository")
 * @ORM\Table(name="notification")
 * @ORM\HasLifecycleCallbacks()
 */
class Notification implements \JsonSerializable
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_read;

    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notification")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notificationMine")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="notificationGroupInvite", cascade={"persist"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $group;



    public function __construct()
    {
        $this->is_read = 0;
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function JsonSerialize()
    {
        return [
          'id' => $this->id,
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
     * Set type
     *
     * @param string $type
     * @return Notification
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set is_read
     *
     * @param boolean $isRead
     * @return Notification
     */
    public function setIsRead($isRead)
    {
        $this->is_read = $isRead;

        return $this;
    }

    /**
     * Get is_read
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->is_read;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Notification
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
     * @return Notification
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
     * Set user
     *
     * @param \Fly\UserBundle\Entity\User $user
     * @return Notification
     */
    public function setUser(\Fly\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Fly\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set sender
     *
     * @param \Fly\UserBundle\Entity\User $sender
     * @return Notification
     */
    public function setSender(\Fly\UserBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Fly\UserBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }

    public function getNotificationType()
    {
        $types = FlyUserBundle::getNoticeTypes();

        return isset($types[$this->getType()])?$types[$this->getType()]:null;
    }

    /**
     * Set group
     *
     * @param \Fly\UserBundle\Entity\Group $group
     * @return Notification
     */
    public function setGroup(\Fly\UserBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Fly\UserBundle\Entity\Group 
     */
    public function getGroup()
    {
        return $this->group;
    }
}
