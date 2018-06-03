<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 */
class Report
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * User who reports
     *
     * @ORM\Column(type="string", length=32)
     */
    private $username;

    /**
     * UUID of listing
     *
     * @ORM\Column(type="string")
     */
    private $listing;

    /**
     * Why it is breaking the rules
     *
     * @ORM\Column(type="text")
     */
    private $offense;

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

    public function getListing()
    {
        return $this->listing;
    }

    public function setListing($listing)
    {
        $this->listing = $listing;
    }

    public function getOffense()
    {
        return $this->offense;
    }

    public function setOffense($offense)
    {
        $this->offense = $offense;
    }
}
