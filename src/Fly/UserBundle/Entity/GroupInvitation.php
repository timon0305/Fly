<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity
 * @ORM\Table(name="group_ivitation")
 * @ORM\HasLifecycleCallbacks()
 */
class GroupInvitation extends BaseGroup
{


    /** @ORM\Id @ORM\Column(type="string", length=6) */
    protected $code;

    /** @ORM\Column(type="string", length=256) */
    protected $email;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $sent = false;


    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="invitation",cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="invitation",cascade={"persist","refresh"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     **/
    private $group;


    public function __construct()
    {
        $code = substr(md5(uniqid(rand(), true)), 0, 6);
        $this->code = $code;
        $this->name = 'invitation_'.$code;
    }



    /**
     * Set code
     *
     * @param string $code
     * @return GroupInvitation
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return GroupInvitation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     * @return GroupInvitation
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean 
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set user
     *
     * @param \Fly\UserBundle\Entity\User $user
     * @return GroupInvitation
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
     * Set group
     *
     * @param \Fly\UserBundle\Entity\Group $group
     * @return GroupInvitation
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
