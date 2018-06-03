<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class BuyerProfile
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
     * The token that the user used to join
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $joinToken;

    /**
     * The token that the user can give out for the affiliate program.
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ORM\Version
     * @var \DateTime
     */
    private $joinDate = null;

    /**
     * 1 enables 2fa, 0 disables
     *
     * @ORM\Column(type="integer", length=1)
     */
    private $twoFactor = 0;

    /**
     * fiat currency to be used
     *
     * @ORM\Column(type="string", length=3)
     */
    private $fiat = "USD";

    /**
     * number of purchases
     *
     * @ORM\Column(type="integer")
     */
    private $totalPurchase = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pgp;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fingerprint;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $profile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $BTCAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $BTCPublic;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $XMRAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $ZECAddress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $profileImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $experience = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $level = 1;

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

    public function getJoinToken()
    {
        return $this->joinToken;
    }

    public function setjoinToken($joinToken)
    {
        $this->joinToken = $joinToken;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
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

    public function getFiat()
    {
        return $this->fiat;
    }

    public function setFiat($fiat)
    {
        $this->fiat = $fiat;
    }

    public function getTotalPurchase()
    {
        return $this->totalPurchase;
    }

    public function setTotalPurchase($totalPurchase)
    {
        $this->totalPurchase = $totalPurchase;
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

    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    public function getBTCAddress()
    {
        return $this->BTCAddress;
    }

    public function setBTCAddress($BTCAddress)
    {
        $this->BTCAddress = $BTCAddress;
    }

    public function getBTCPublic()
    {
        return $this->BTCPublic;
    }

    public function setBTCPublic($BTCPublic)
    {
        $this->BTCPublic = $BTCPublic;
    }

    public function getXMRAddress()
    {
        return $this->XMRAddress;
    }

    public function setXMRAddress($XMRAddress)
    {
        $this->XMRAddress = $XMRAddress;
    }

    public function getZECAddress()
    {
        return $this->ZECAddress;
    }

    public function setZECAddress($ZECAddress)
    {
        $this->ZECAddress = $ZECAddress;
    }

    public function getProfileImage()
    {
        return $this->profileImage;
    }

    public function setProfileImage($profileImage)
    {
        $this->profileImage = $profileImage;
    }

    public function getExperience()
    {
        return $this->experience;
    }

    public function setExperience($experience)
    {
        $this->experience = $experience;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }
}
