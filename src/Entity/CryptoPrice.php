<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 */
class CryptoPrice
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
    private $crypto;

    /**
     * AUD
     * CAD
     * CHF
     * EUR
     * GBP
     * NZD
     * USD
     *
     * @ORM\Column(type="string")
     */
    private $fiat;

    /**
     * Price compared to fiat of crypto
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $price;

    /**
     * UNIX timestamp in seconds
     *
     * @ORM\Column(type="integer")
     */
    private $time;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getCrypto()
    {
        return $this->crypto;
    }

    public function setCrypto($crypto)
    {
        $this->crypto = $crypto;
    }

    public function getFiat()
    {
        return $this->fiat;
    }

    public function setFiat($fiat)
    {
        $this->fiat = $fiat;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }
}
