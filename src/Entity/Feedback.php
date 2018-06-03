<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 */
class Feedback
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
    private $buyer;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank()
     */
    private $vendor;

    /**
     * UUID of listing
     *
     * @ORM\Column(type="string")
     */
    private $listing;

    /**
     * UUID of order
     *
     * @ORM\Column(type="string")
     */
    private $fOrder;

    /**
     * Feedback
     *
     * @ORM\Column(type="string")
     */
    private $feedback;

    /**
     * Comment
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getBuyer()
    {
        return $this->buyer;
    }

    public function setBuyer($buyer)
    {
        $this->buyer = $buyer;
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    public function getListing()
    {
        return $this->listing;
    }

    public function setListing($listing)
    {
        $this->listing = $listing;
    }

    public function getOrder()
    {
        return $this->fOrder;
    }

    public function setfOrder($order)
    {
        $this->fOrder = $order;
    }

    public function getFeedback()
    {
        return $this->feedback;
    }

    public function setFeedback($feedback)
    {
        $this->feedback = $feedback;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }
}
