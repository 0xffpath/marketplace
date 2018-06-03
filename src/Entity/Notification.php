<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * User who the message belongs
     *
     * @ORM\Column(type="string", length=32)
     */
    private $username;

    /**
     * Title
     *
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @ORM\Version
     * @var \DateTime
     */
    private $notificationDate = null;

    /**
     * Amount of order
     *
     * @ORM\Column(type="text")
     */
    private $amount;

    /**
     * Type of notification
     *
     * confirmation - waiting for blockchain to confirm
     * pending - waiting for vendor to accept
     * accepted - vendor accepted order
     * rejected - vendor rejected order
     * timeout - vendor failed to accept after 3 days
     * canceled - vendor canceled order
     * shipped - vendor shipped item
     * finalized - user has finalized order
     * auto - order auto-finalized
     * vendor - money refunded to vendor
     * buyer  - money refunded to buyer
     *
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * Bootstrap of notification
     *
     * confirmation - waiting for blockchain to confirm
     * pending - primary
     * accepted - success
     * accepted - success
     * timeout - warning
     * canceled - danger
     * shipped - success
     * finalized - success
     * auto - secondary
     * vendor - warning
     * buyer  - warning
     *
     * @ORM\Column(type="string")
     */
    private $bootstrap;

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getNotificationDate()
    {
        return $this->notificationDate;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }
}
