<?php

namespace Fly\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity(repositoryClass="Fly\PlatformBundle\Entity\AccCategoryRepository")
 * @ORM\Table(name="acc_category")
 * @ORM\HasLifecycleCallbacks()
 */
class AccCategory implements \JsonSerializable
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
     * @ORM\OneToMany(targetEntity="Acc", mappedBy="accCategory", cascade={"persist","remove"})
     **/
    private $acc;

//    /**
//     * @ORM\ManyToMany(targetEntity="Acc", mappedBy="accCategory")
//     **/
//    private $acc;

//    /**
//     * @ORM\ManyToMany(targetEntity="Acc", inversedBy="accCategory")
//     * @ORM\JoinTable(name="acc_has_category")
//     **/
//    private $acc;



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
//            'created_timastamp'=>$this->getCreated()->getTimestamp(),
//            'updated_timestamp'=>$this->getCreated()->getTimestamp(),
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
     * @return AccCategory
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
     * @return AccCategory
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
     * @return AccCategory
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
     * Add acc
     *
     * @param \Fly\PlatformBundle\Entity\Acc $acc
     * @return AccCategory
     */
    public function addAcc(\Fly\PlatformBundle\Entity\Acc $acc)
    {
        $this->acc[] = $acc;

        return $this;
    }

    /**
     * Remove acc
     *
     * @param \Fly\PlatformBundle\Entity\Acc $acc
     */
    public function removeAcc(\Fly\PlatformBundle\Entity\Acc $acc)
    {
        $this->acc->removeElement($acc);
    }

    /**
     * Get acc
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAcc()
    {
        return $this->acc;
    }
}
