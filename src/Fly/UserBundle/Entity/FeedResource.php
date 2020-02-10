<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity
 * @ORM\Table(name="feed_resource")
 * @ORM\HasLifecycleCallbacks()
 */
class FeedResource
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
     * @ORM\Column(name="type",type="string")
     */
    protected $type;

    /**
     * @ORM\Column(name="title",type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(name="description",type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="image",type="text", nullable=true)
     */
    private $image;


    /**
     * @ORM\Column(name="thumb",type="text", nullable=true)
     */
    private $thumb;


    /**
     * @ORM\Column(name="resourceUrl",type="text", nullable=true)
     */
    private $resourceUrl;

    /**
     * @ORM\Column(name="embedId",type="text", nullable=true)
     */
    private $embedId;


    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;



    /**
     * @ORM\ManyToOne(targetEntity="Feed", inversedBy="resource")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $feed;


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
     * Set type
     *
     * @param string $type
     * @return FeedResource
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
     * Set title
     *
     * @param string $title
     * @return FeedResource
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
     * Set description
     *
     * @param string $description
     * @return FeedResource
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
     * Set image
     *
     * @param string $image
     * @return FeedResource
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set thumb
     *
     * @param string $thumb
     * @return FeedResource
     */
    public function setThumb($thumb)
    {
        $this->thumb = $thumb;

        return $this;
    }

    /**
     * Get thumb
     *
     * @return string 
     */
    public function getThumb()
    {
        return $this->thumb;
    }

    /**
     * Set resourceUrl
     *
     * @param string $resourceUrl
     * @return FeedResource
     */
    public function setResourceUrl($resourceUrl)
    {
        $this->resourceUrl = $resourceUrl;

        return $this;
    }

    /**
     * Get resourceUrl
     *
     * @return string 
     */
    public function getResourceUrl()
    {
        return $this->resourceUrl;
    }


    /**
     * Set embedId
     *
     * @param string $embedId
     * @return FeedResource
     */
    public function setEmbedId($embedId)
    {
        $this->embedId = $embedId;

        return $this;
    }

    /**
     * Get embedId
     *
     * @return string
     */
    public function getEmbedId()
    {
        return $this->embedId;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return FeedResource
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
     * @return FeedResource
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
     * @return FeedResource
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
}
