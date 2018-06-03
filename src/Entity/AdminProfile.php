<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class AdminProfile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ORM\Version
     * @var \DateTime
     */
    private $joinDate = null;

    /**
     * fiat currency to be used
     *
     * @ORM\Column(type="string", length=3)
     */
    private $currency = "USD";

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pgp;

    /**
     * 1 enables 2fa, 0 disables
     *
     * @ORM\Column(type="integer", length=1)
     */
    private $twoFactor = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fingerprint;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $profileImage;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getJoinDate()
    {
        return $this->joinDate;
    }

    public function setJoinDate($joinDate)
    {
        $this->joinDate = $joinDate;
    }

    public function getTwoFactor()
    {
        return $this->twoFactor;
    }

    public function setTwoFactor($twoFactor)
    {
        $this->twoFactor = $twoFactor;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getPGP()
    {
        return $this->pgp;
    }

    public function setPGP($pgp)
    {
        $this->pgp = $pgp;
    }

    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    public function setFingerprint($fingerprint)
    {
        $this->fingerprint = $fingerprint;
    }

    public function getProfileImage()
    {
        return $this->profileImage;
    }

    public function setProfileImage($profileImage)
    {
        $this->profileImage = $profileImage;
    }
}
