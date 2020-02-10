<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\ActCategoryRepository")
 * @ORM\Table(name="act_category")
 * @ORM\HasLifecycleCallbacks()
 */
class ActCategory implements \JsonSerializable
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
     * @ORM\OneToMany(targetEntity="Act", mappedBy="actCategory", cascade={"persist","remove"})
     **/
    private $act;




    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function __toString()
    {
        return $this->title;
    }



    public function jsonSerialize()
    {
        return [
            'id'=>$this->id,
            'title'=>$this->title,
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
     * Set title
     *
     * @param string $title
     * @return ActCategory
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
     * @return ActCategory
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
     * @return ActCategory
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
     * Add act
     *
     * @param \Fly\PlatformBundle\Entity\Act $act
     * @return ActCategory
     */
    public function addAct(\Fly\PlatformBundle\Entity\Act $act)
    {
        $this->act[] = $act;

        return $this;
    }

    /**
     * Remove act
     *
     * @param \Fly\PlatformBundle\Entity\Act $act
     */
    public function removeAct(\Fly\PlatformBundle\Entity\Act $act)
    {
        $this->act->removeElement($act);
    }

    /**
     * Get act
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAct()
    {
        return $this->act;
    }
}
