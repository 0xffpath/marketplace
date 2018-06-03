<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class VendorProfile
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
     * @ORM\Column(type="integer")
     */
    private $joinDate = null;

    /**
     * @ORM\Column(type="integer")
     */
    private $lastSeen = 0;

    /**
     * 1 enables 2fa, 0 disables
     *
     * @ORM\Column(type="integer", length=1)
     */
    private $twoFactor = 0;

    /**
     * total feedback
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalFeedback = 0;

    /**
     * total positive
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $positive = 0;

    /**
     * total neutral
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $neutral = 0;

    /**
     * total negative
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $negative = 0;

    /**
     * fiat currency to be used
     *
     * @ORM\Column(type="string", length=3)
     */
    private $fiat = "USD";

    /**
     * number of successful transactions
     *
     * @ORM\Column(type="integer")
     */
    private $totalSell = 0;

    /**
     * is verified
     *
     * @ORM\Column(type="boolean")
     */
    private $verified = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pgp;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fingerprint;

    /**
     * terms and conditions
     * @ORM\Column(type="text", nullable=true)
     */
    private $tac;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $profile;

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

    public function getJoinDate()
    {
        return $this->joinDate;
    }

    public function setJoinDate($joinDate)
    {
        $this->joinDate = $joinDate;
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

    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;
    }

    public function getTwoFactor()
    {
        return $this->twoFactor;
    }

    public function setTwoFactor($twoFactor)
    {
        $this->twoFactor = $twoFactor;
    }

    public function getTotalFeedback()
    {
        return $this->totalFeedback;
    }

    public function setTotalFeedback($totalFeedback)
    {
        $this->totalFeedback = $totalFeedback;
    }

    public function getPositive()
    {
        return $this->positive;
    }

    public function setPositive($positive)
    {
        $this->positive = $positive;
    }

    public function getNeutral()
    {
        return $this->neutral;
    }

    public function setNeutral($neutral)
    {
        $this->neutral = $neutral;
    }

    public function getNegative()
    {
        return $this->negative;
    }

    public function setNegative($negative)
    {
        $this->negative = $negative;
    }

    public function getFiat()
    {
        return $this->fiat;
    }

    public function setFiat($fiat)
    {
        $this->fiat = $fiat;
    }

    public function getTotalSell()
    {
        return $this->totalSell;
    }

    public function setTotalSell($totalSell)
    {
        $this->totalSell = $totalSell;
    }

    public function getVerified()
    {
        return $this->verified;
    }

    public function setVerified($verified)
    {
        $this->verified = $verified;
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

    public function getTac()
    {
        return $this->tac;
    }

    public function setTac($tac)
    {
        $this->tac = $tac;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile($profile)
    {
        $this->profile = $profile;
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
