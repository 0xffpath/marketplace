<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 */
class Orders
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
     * UUID of listing
     *
     * @ORM\Column(type="string")
     */
    private $listing;

    /**
     * confirmation - waiting for blockchain to confirm
     * pending - waiting for vendor to accept
     * accepted - vendor accepted order
     * rejected - vendor rejected order
     * timeout - vendor failed to accept after 3 days
     * canceled - vendor canceled order
     * shipped - vendor shipped items
     * finalized - user has finalized order
     * disputed - user has disputed the order
     * auto - order auto-finalized
     *
     * @ORM\Column(type="string")
     */
    private $status = 'confirmation';

    /**
     * number item bought
     *
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * Title of item
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * User who bought item
     *
     * @ORM\Column(type="string")
     */
    private $buyer;

    /**
     * vendor of item
     *
     * @ORM\Column(type="string")
     */
    private $vendor;

    /**
     * Address of cryptocurrency
     *
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * Reedem script for Bitcoin
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $redeem;

    /**
     * price per item * amount
     *
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     */
    private $price;

    /**
     * dollar fee
     *
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     */
    private $fee;

    /**
     * crypto fee
     *
     * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     */
    private $cryptoFee;

    /**
     * price per item in crypto * amount
     *
     * @ORM\Column(type="decimal", precision=8, scale=7)
     */
    private $cryptoPrice;

    /**
     * price of shipping
     *
     * @ORM\Column(type="decimal", precision=7, scale=2)
     */
    private $shippingPrice;

    /**
     * price of shipping in crypto
     *
     * @ORM\Column(type="decimal", precision=8, scale=7)
     */
    private $shippingCryptoPrice;


    /**
     * null - not shipped
     * transit - currently being shipped
     * delivered - has been delivered
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $shippingStatus;

    /**
     * name of shipping
     * ex: First Class 1-3 day delivery
     *
     * @ORM\Column(type="string")
     */
    private $shippingType;


    /**
     * ID of shipping option
     *
     * @ORM\Column(type="string")
     */
    private $shippingOption;

    /**
     * date that vendor shipped item
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $shippedDate;

    /**
     * price + shipping price
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $total;

    /**
     * crypto price + crypto shipping price
     *
     * @ORM\Column(type="decimal", precision=8, scale=7)
     */
    private $cryptoTotal;

    /**
     * xmr, btc, bcc, eth, zec
     *
     * @ORM\Column(type="string")
     */
    private $crypto;

    /**
     * date that order will auto-finalize
     * shipped + 3 days
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $autoDate;

    /**
     *
     * confirmation - warning
     * pending - primary
     * accepted - success
     * rejected - danger
     * timeout - warning
     * canceled - danger
     * shipped - success
     * finalized - success
     * disputed - danger
     * auto - secondary
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $bootstrap;

    /**
     * date user bought the item
     *
     * @ORM\Column(type="integer")
     */
    private $startDate;

    /**
     * received
     *
     * @ORM\Column(type="boolean")
     */
    private $recieved = false;

    /**
     * confirmed by blockchain
     *
     * @ORM\Column(type="boolean")
     */
    private $confirmed = false;

    /**
     * type of multisig
     *
     * @ORM\Column(type="string")
     */
    private $multisig;

    /**
     * Fiat currency that order is in
     *
     * @ORM\Column(type="string")
     */
    private $fiat;

    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $reviewed = false;

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

    public function getListing()
    {
        return $this->listing;
    }

    public function setListing($listing)
    {
        $this->listing = $listing;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
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

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getRedeem()
    {
        return $this->redeem;
    }

    public function setRedeem($redeem)
    {
        $this->redeem = $redeem;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getFee()
    {
        return $this->fee;
    }

    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    public function getCryptoFee()
    {
        return $this->cryptoFee;
    }

    public function setCryptoFee($cryptoFee)
    {
        $this->cryptoFee = $cryptoFee;
    }

    public function getCryptoPrice()
    {
        return $this->cryptoPrice;
    }

    public function setCryptoPrice($cryptoPrice)
    {
        $this->cryptoPrice = $cryptoPrice;
    }

    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    public function setShippingPrice($shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;
    }

    public function getShippingCryptoPrice()
    {
        return $this->shippingCryptoPrice;
    }

    public function setShippingCryptoPrice($shippingCryptoPrice)
    {
        $this->shippingCryptoPrice = $shippingCryptoPrice;
    }

    public function getShippingStatus()
    {
        return $this->shippingStatus;
    }

    public function setShippingStatus($shippingStatus)
    {
        $this->shippingStatus = $shippingStatus;
    }

    public function getShippingType()
    {
        return $this->shippingType;
    }

    public function setShippingType($shippingType)
    {
        $this->shippingType = $shippingType;
    }

    public function getShippingOption()
    {
        return $this->shippingOption;
    }

    public function setShippingOption($shippingOption)
    {
        $this->shippingOption = $shippingOption;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function getCryptoTotal()
    {
        return $this->cryptoTotal;
    }

    public function setCryptoTotal($cryptoTotal)
    {
        $this->cryptoTotal = $cryptoTotal;
    }

    public function getCrypto()
    {
        return $this->crypto;
    }

    public function setCrypto($crypto)
    {
        $this->crypto = $crypto;
    }

    public function getAutoDate()
    {
        return $this->autoDate;
    }

    public function setAutoDate($autoDate)
    {
        $this->autoDate = $autoDate;
    }

    public function getBootstrap()
    {
        return $this->bootstrap;
    }

    public function setBootstrap($bootstrap)
    {
        $this->bootstrap = $bootstrap;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    public function getShippedDate()
    {
        return $this->shippedDate;
    }

    public function setShippedDate($shippedDate)
    {
        $this->shippedDate = $shippedDate;
    }

    public function getRecieved()
    {
        return $this->recieved;
    }

    public function setRecieved($recieved)
    {
        $this->recieved = $recieved;
    }

    public function getConfirmed()
    {
        return $this->confirmed;
    }

    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    public function getMultisig()
    {
        return $this->multisig;
    }

    public function setMultisig($multiSig)
    {
        $this->multisig = $multiSig;
    }

    public function getFiat()
    {
        return $this->fiat;
    }

    public function setFiat($fiat)
    {
        $this->fiat = $fiat;
    }

    public function getReviewed()
    {
        return $this->reviewed;
    }

    public function setReviewed($reviewed)
    {
        $this->reviewed = $reviewed;
    }
}
