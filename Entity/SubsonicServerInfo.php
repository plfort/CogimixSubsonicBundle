<?php
namespace Cogipix\CogimixSubsonicBundle\Entity;
use Cogipix\CogimixCommonBundle\Entity\User;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DoctrineEncrypt\Configuration\Encrypted;
/**
 *
 * @author plfort - Cogipix
 * @ORM\Entity
 * @UniqueEntity(fields="alias",message="cogimix.subsonic_server_alias_already_used",groups={"Create"})
 */
class SubsonicServerInfo
{

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message="This value should not be blank",groups={"Create","Edit"})
     * @Assert\Length(min=3, max=30,minMessage="field_too_short", maxMessage="field_too_long", groups={"Create","Edit"})
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Length(min=4, max=20,minMessage="field_too_short", maxMessage="field_too_long", groups={"Create","Edit"})
     * @Assert\NotBlank(message="This value should not be blank",groups={"Create","Edit"})
     * @Assert\Regex(pattern="/^\w*$/",message="error_alphanum", groups={"Create","Edit"})
     */
    protected $alias;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(message="This value should not be blank",groups={"Create","Edit","Test"})
     * @Assert\Url(groups={"Create","Edit","Test"})
     */
    protected $endPointUrl;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="This value should not be blank",groups={"Create","Test"})
     */
    protected $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="This value should not be blank",groups={"Create","Test"})
     * @Encrypted
     */
    protected $password;

    /**
     * @ORM\ManyToOne(targetEntity="Cogipix\CogimixCommonBundle\Entity\User")
     * @var unknown_type
     */
    protected $user;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    public function getEndPointUrl()
    {
        return $this->endPointUrl;
    }

    public function setEndPointUrl($endPointUrl)
    {
        $this->endPointUrl = $endPointUrl;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

}
