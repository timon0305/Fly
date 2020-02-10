<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="feed_comment")
 * use repository for handy tree functions
 * @ORM\Entity(repositoryClass="FeedCommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */

class FeedComment implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Feed", inversedBy="comment")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $feed;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="comment")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $user;


    /**
     * @ORM\Column(name="content",type="text")
     */
    protected $content;

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
     * Set content
     *
     * @param string $content
     * @return FeedComment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return FeedComment
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
     * @return FeedComment
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
     * Set feed
     *
     * @param \Fly\UserBundle\Entity\Feed $feed
     * @return FeedComment
     */
    public function setFeed(\Fly\UserBundle\Entity\Feed $feed = null)
    {
        $this->feed = $feed;

        return $this;
    }

    /**
     * Get feed
     *
     * @return \Fly\UserBundle\Entity\Feed 
     */
    public function getFeed()
    {
        return $this->feed;
    }

    public function setParent(FeedComment $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }



    /**
     * Set user
     *
     * @param \Fly\UserBundle\Entity\User $user
     * @return FeedComment
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


    public function isOwner($user)
    {
        return $this->getUser() == $user;
    }


    public function jsonSerialize()
    {
        return [
            'id'=>$this->id,
            'content'=>$this->content,
            'date'=>$this->getUpdated()->format('F d, Y').' at '.$this->getUpdated()->format('H:i'),
            'feed'=>$this->getFeed(),
            'user'=>$this->getUser(),
        ];
    }
}
