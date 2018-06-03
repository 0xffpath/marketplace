<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 */
class Shipping
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * UUID of listing
     *
     * @ORM\Column(type="string")
     */
    private $listing;

    /**
     * ID of shipping option
     *
     * @ORM\Column(type="integer")
     */
    private $shippingOption;

    public function getId()
    {
        return $this->id;
    }

    public function getListing()
    {
        return $this->listing;
    }

    public function setListing($listing)
    {
        $this->listing = $listing;
    }

    public function getShippingOption()
    {
        return $this->shippingOption;
    }

    public function setShippingOption($shippingOption)
    {
        $this->shippingOption = $shippingOption;
    }
}
