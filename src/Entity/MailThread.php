<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class MailThread
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $uuid;

    /**
     * Username of person in thread
     *
     * @ORM\Column(type="string", length=32)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $subject;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ORM\Version
     * @var \DateTime
     */
    private $startDate = null;

    /**
     * Used to sort by time.
     * Uses microtime() in PHP.
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $lastMessage = 0;

    /**
     * 0 = not seen
     * 1 = seen
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $seen = 0;

    /**
     * 0 = not an order
     * else it is an order
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $orderId = 0;

    public function getId()
    {
        return $this->id;
    }

    public function getUUID()
    {
        return $this->uuid;
    }

    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setLastMessage($lastMessage)
    {
        $this->lastMessage = $lastMessage;
    }

    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    public function setSeen($seen)
    {
        $this->seen = $seen;
    }

    public function getSeen()
    {
        return $this->seen;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }
}
