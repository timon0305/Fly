<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\UserBundle\Entity\FeedRepository")
 * @ORM\Table(name="feed")
 * @ORM\HasLifecycleCallbacks()
 */
class Feed implements \JsonSerializable
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
     * @ORM\Column(name="description",type="text")
     */
    protected $description;



    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $picture;


    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;



    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="feed")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\OrderBy({"updated" = "DESC"})
     **/
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="feed")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="FeedCategory", inversedBy="feed")
     * @ORM\JoinColumn(name="feed_category_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $feedCategory;

    /**
     * @ORM\OneToMany(targetEntity="FeedResource", mappedBy="feed", cascade={"persist","remove"})
     **/
    private $resource;

    /**
     * @ORM\OneToMany(targetEntity="FeedComment", mappedBy="feed", cascade={"persist","remove"})
     **/
    private $comment;

    /**
     * @ORM\OneToMany(targetEntity="FeedLike", mappedBy="feed", cascade={"persist","remove"})
     **/
    private $like;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="feedViews",  cascade={"persist"})
     * @ORM\JoinTable(name="feed_user_views")
     **/
    private $userViews;


    public function __construct()
    {
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
     * Set description
     *
     * @param string $description
     * @return Feed
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
     * Set picture
     *
     * @param string $picture
     * @return Feed
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

    /**
     * Set group
     *
     * @param \Fly\UserBundle\Entity\Group $group
     * @return Feed
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

    /**
     * Set user
     *
     * @param \Fly\UserBundle\Entity\User $user
     * @return Feed
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
     * Add resource
     *
     * @param \Fly\UserBundle\Entity\FeedResource $resource
     * @return Feed
     */
    public function addResource(\Fly\UserBundle\Entity\FeedResource $resource)
    {
        $this->resource[] = $resource;

        return $this;
    }

    /**
     * Remove resource
     *
     * @param \Fly\UserBundle\Entity\FeedResource $resource
     */
    public function removeResource(\Fly\UserBundle\Entity\FeedResource $resource)
    {
        $this->resource->removeElement($resource);
    }

    /**
     * Get resource
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Add comment
     *
     * @param \Fly\UserBundle\Entity\FeedComment $comment
     * @return Feed
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

//    public function jsonSerialize()
//    {
//        return [
//            'id'=>$this->id,
//            'content'=>$this->content,
//            'date'=>$this->getUpdated()->format('m d, Y at h:i'),
//            'feed'=>$this->getFeed(),
//            'user'=>$this->getUser(),
//
//        ];
//    }

    /**
     * Add like
     *
     * @param \Fly\UserBundle\Entity\FeedLike $like
     * @return Feed
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


    public function jsonSerialize()
    {
        return [
            'id'=>$this->id,
            'description'=>$this->description,
            'picture'=>$this->picture,
            'date'=>$this->getUpdated()->format('F d, Y').' at '.$this->getUpdated()->format('H:i'),
            'created_timastamp'=>$this->getCreated()->getTimestamp(),
            'updated_timestamp'=>$this->getCreated()->getTimestamp(),
            'user'=>$this->getUser(),
        ];
    }


    /**
     * Add userViews
     *
     * @param \Fly\UserBundle\Entity\User $userViews
     * @return Feed
     */
    public function addUserView(\Fly\UserBundle\Entity\User $userViews)
    {
        $this->userViews[] = $userViews;

        return $this;
    }

    /**
     * Remove userViews
     *
     * @param \Fly\UserBundle\Entity\User $userViews
     */
    public function removeUserView(\Fly\UserBundle\Entity\User $userViews)
    {
        $this->userViews->removeElement($userViews);
    }

    /**
     * Get userViews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserViews()
    {
        return $this->userViews;
    }



    /**
     * Set feedCategory
     *
     * @param \Fly\UserBundle\Entity\FeedCategory $feedCategory
     * @return Feed
     */
    public function setFeedCategory(\Fly\UserBundle\Entity\FeedCategory $feedCategory = null)
    {
        $this->feedCategory = $feedCategory;

        return $this;
    }

    /**
     * Get feedCategory
     *
     * @return \Fly\UserBundle\Entity\FeedCategory 
     */
    public function getFeedCategory()
    {
        return $this->feedCategory;
    }
}
