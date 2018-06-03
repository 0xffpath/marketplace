<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 */
class ListingImages
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Name of image. Ex: 12345.jpg
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $image;

    /**
     * Vendor of listing
     *
     * @ORM\Column(type="string", length=32)
     */
    private $vendor;

    /**
     * UUID of listing
     *
     * @ORM\Column(type="string")
     */
    private $listing;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
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
}
