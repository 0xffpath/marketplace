<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     */
    private $auto = null;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_]+$/")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $role;

    /**
     * @Assert\Length(max=4096)
     * @Assert\NotBlank()
     */
    private $plainPassword;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * Pin for withdraw
     *
     * @Assert\Length(min=3, max=6)
     * @ORM\Column(type="integer", length=6)
     */
    private $pin;

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRoles()
    {
        return [$this->role];
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getPin()
    {
        return $this->pin;
    }

    public function setPin($pin)
    {
        $this->pin = $pin;
    }

    public function getSalt()
    {
        return null;
    }

    public function getAuto()
    {
        return $this->auto;
    }

    public function setAuto($auto)
    {
        $this->auto = $auto;
    }

    public function eraseCredentials()
    {
        return null;
    }
}
