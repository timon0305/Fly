<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\UserBundle\Entity\FriendsRepository")
 * @ORM\Table(name="friends")
 * @ORM\HasLifecycleCallbacks()
 */
class Friends
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="friend_one", referencedColumnName="id")
     **/
    private $friend_one;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="friend_two", referencedColumnName="id")
     **/
    private $friend_two;

    /**
     * @var integer
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    // statuses: 0 - Pending Friend Request, 1 - Confirm Friend Request, 2 - You, 3 - Unfriend
    protected $status;


    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;


    public function __construct()
    {
        $this->status = 0;
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
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
     * Set status
     *
     * @param integer $status
     * @return Friends
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set friend_one
     *
     * @param \Fly\UserBundle\Entity\User $friendOne
     * @return Friends
     */
    public function setFriendOne(\Fly\UserBundle\Entity\User $friendOne = null)
    {
        $this->friend_one = $friendOne;

        return $this;
    }

    /**
     * Get friend_one
     *
     * @return \Fly\UserBundle\Entity\User 
     */
    public function getFriendOne()
    {
        return $this->friend_one;
    }

    /**
     * Set friend_two
     *
     * @param \Fly\UserBundle\Entity\User $friendTwo
     * @return Friends
     */
    public function setFriendTwo(\Fly\UserBundle\Entity\User $friendTwo = null)
    {
        $this->friend_two = $friendTwo;

        return $this;
    }

    /**
     * Get friend_two
     *
     * @return \Fly\UserBundle\Entity\User 
     */
    public function getFriendTwo()
    {
        return $this->friend_two;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Feed
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
     * @return Feed
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
}
