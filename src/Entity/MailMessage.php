<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class MailMessage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * UUID of thread
     *
     * @ORM\Column(type="string")
     */
    private $thread;

    /**
     * User who the message belongs
     *
     * @ORM\Column(type="string", length=32)
     */
    private $username;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ORM\Version
     * @var \DateTime
     */
    private $messageDate = null;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    public function getId()
    {
        return $this->id;
    }

    public function getThread()
    {
        return $this->thread;
    }

    public function setThread($thread)
    {
        $this->thread = $thread;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getMessageDate()
    {
        return $this->messageDate;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
}
