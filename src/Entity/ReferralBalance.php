<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class ReferralBalance
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="decimal")
     */
    private $moneroBalance = 0;

    /**
     * @ORM\Column(type="decimal")
     */
    private $zcashBalance = 0;

    public function getThreadId()
    {
        return $this->threadId;
    }

    public function setThreadId($threadId)
    {
        $this->threadId = $threadId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getMoneroBalance()
    {
        return $this->moneroBalance;
    }

    public function setReadMessage($moneroBalance)
    {
        $this->moneroBalance = $moneroBalance;
    }

    public function getZcashBalance()
    {
        return $this->zcashBalance;
    }

    public function setZcashBalance($zcashBalance)
    {
        $this->zcashBalance = $zcashBalance;
    }
}
