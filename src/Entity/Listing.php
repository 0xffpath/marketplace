<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 */
class Listing
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
     * 0 means no parent
     * >0 id of parent
     *
     * @ORM\Column(type="string", options={"default" : 0})
     */
    private $parent = 0;


    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * false = not reported
     * true = reported
     *
     * @ORM\Column(type="integer")
     */
    private $flag = false;

    /**
     * Reason flagged
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $flagReason;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $category;

    /**
     * @ORM\Column(type="integer")
     */
    private $parentCategory;

    /**
     * @ORM\Column(type="text", length=10000)
     */
    private $excludeCountries;

    /**
     * @ORM\Column(type="text")
     */
    private $fromCountry;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * Rating out of 5
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating = null;

    /**
     * Discount in units of money
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $discount = 0;

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
     * false = currency not allowed
     * true = currency allowed
     * @ORM\Column(type="boolean")
     */
    private $btc = false;

    /**
     * false = currency not allowed
     * true = currency allowed
     * @ORM\Column(type="boolean")
     */
    private $xmr = false;

    /**
     * false = currency not allowed
     * true = currency allowed
     * @ORM\Column(type="boolean")
     */
    private $zec = false;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * 1 = category
     * 2 = info
     * 3 = pricing
     * 4 = shipping
     * 5 = image
     * 6 = description
     * 7 = search (FINAL)
     *
     * @ORM\Column(type="integer")
     */
    private $step = 1;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $keywords;

    /**
     * NULL = unlimited
     * How many items the listing has
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stock;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUUID()
    {
        return $this->uuid;
    }

    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getFlag()
    {
        return $this->flag;
    }

    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    public function getFlagReason()
    {
        return $this->flagReason;
    }

    public function setFlagReason($flagReason)
    {
        $this->flagReason = $flagReason;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getParentCategory()
    {
        return $this->parentCategory;
    }

    public function setParentCategory($parentCategory)
    {
        $this->parentCategory = $parentCategory;
    }

    public function getExcludeCountries()
    {
        return $this->excludeCountries;
    }

    public function setExcludeCountries($excludeCountries)
    {
        $this->excludeCountries = $excludeCountries;
    }

    public function getFromCountry()
    {
        return $this->fromCountry;
    }

    public function setFromCountry($fromCountry)
    {
        $this->fromCountry = $fromCountry;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function getFiat()
    {
        return $this->fiat;
    }

    public function setFiat($fiat)
    {
        $this->fiat = $fiat;
    }

    public function getBTC()
    {
        return $this->btc;
    }

    public function setBTC($btc)
    {
        $this->btc = $btc;
    }

    public function getXMR()
    {
        return $this->xmr;
    }

    public function setXMR($xmr)
    {
        $this->xmr = $xmr;
    }

    public function getZEC()
    {
        return $this->zec;
    }

    public function setZEC($zec)
    {
        $this->zec = $zec;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getStep()
    {
        return $this->step;
    }

    public function setStep($step)
    {
        $this->step = $step;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }
}
