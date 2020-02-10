<?php

namespace Fly\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * @ORM\Entity
 * @ORM\Table(name="invitation")
 * @ORM\HasLifecycleCallbacks()
 */
class Invitation extends BaseGroup
{


    /** @ORM\Id @ORM\Column(type="string", length=6) */
    protected $code;

    /** @ORM\Column(type="string", length=256) */
    protected $email;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $sent = false;

    /** @ORM\OneToOne(targetEntity="User", mappedBy="invitation", cascade={"persist", "merge"}) */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="invitation",cascade={"persist"})
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     **/
    private $group;


    public function __construct()
    {
        $code = substr(md5(uniqid(rand(), true)), 0, 6);
        $this->code = $code;
        $this->name = 'invitation_'.$code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isSent()
    {
        return $this->sent;
    }

    public function send()
    {
        $this->sent = true;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }


    /**
     * Set code
     *
     * @param string $code
     * @return Invitation
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     * @return Invitation
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
     * Set group
     *
     * @param \Fly\UserBundle\Entity\Group $group
     * @return Invitation
     */
    public function setGroup(\Fly\UserBundle\Entity\Group $group = null)
    {
//        if($group){
//
//        }
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

    public function __toString()
    {
        return $this->email;
    }
}
