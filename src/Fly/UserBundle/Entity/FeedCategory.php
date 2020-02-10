<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\UserBundle\Entity\FeedCategoryRepository")
 * @ORM\Table(name="feed_catrgory")
 * @ORM\HasLifecycleCallbacks()
 */
class FeedCategory implements \JsonSerializable
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;


    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;



    /**
     * @ORM\OneToMany(targetEntity="Feed", mappedBy="feedCategory", cascade={"persist","remove"})
     **/
    private $feed;


    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function __toString()
    {
        return $this->title;
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
     * Set title
     *
     * @param string $title
     * @return Feed
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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


    public function jsonSerialize()
    {
        return [
            'id'=>$this->id,
            'description'=>$this->title,
            'created_timastamp'=>$this->getCreated()->getTimestamp(),
            'updated_timestamp'=>$this->getCreated()->getTimestamp(),
        ];
    }


    /**
     * Add feed
     *
     * @param \Fly\UserBundle\Entity\Feed $feed
     * @return FeedCategory
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
}
