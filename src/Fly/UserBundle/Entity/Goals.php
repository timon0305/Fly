<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity
 * @ORM\Table(name="goals")
 * @ORM\HasLifecycleCallbacks()
 */
class Goals
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
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=false,nullable=true)
     */
    private $slug;



    /**
     * @ORM\Column(name="title",type="string")
     */
    protected $title;

    /**
     * @ORM\Column(name="goal_date", type="datetime", nullable=true)
     */
    protected $goal_date;

    /**
     * @ORM\Column(name="checked", type="boolean", nullable=true)
     */
    protected $checked;


    /**
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    protected $updated;



    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="goal", cascade={"persist","remove"})
     **/
    private $group;


    public function __construct()
    {
        $this->created = new \DateTime();
        $this->group = new ArrayCollection();
    }


    /**
     * Set goal_date
     *
     * @param \DateTime $goalDate
     * @return Goals
     */
    public function setGoalDate($goalDate)
    {
        $this->goal_date = $goalDate;

        return $this;
    }

    /**
     * Get goal_date
     *
     * @return \DateTime 
     */
    public function getGoalDate()
    {
        return $this->goal_date;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Goals
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
     * @return Goals
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set checked
     *
     * @param boolean $checked
     * @return Goals
     */
    public function setChecked($checked)
    {
        $this->checked = $checked;

        return $this;
    }

    /**
     * Get checked
     *
     * @return boolean 
     */
    public function getChecked()
    {
        return $this->checked;
    }


    /**
     * Set slug
     *
     * @param string $slug
     * @return Goals
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Goals
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
     * Add group
     *
     * @param \Fly\UserBundle\Entity\Group $group
     * @return Goals
     */
    public function addGroup(\Fly\UserBundle\Entity\Group $group)
    {
        $this->group[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \Fly\UserBundle\Entity\Group $group
     */
    public function removeGroup(\Fly\UserBundle\Entity\Group $group)
    {
        $this->group->removeElement($group);
    }

    /**
     * Get group
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroup()
    {
        return $this->group;
    }
}
