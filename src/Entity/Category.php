<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=28)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * 0 = sub category
     * 1 = main category
     *
     * @ORM\Column(type="integer")
     */
    private $sub = 1;

    /**
     * ID of parent if sub
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $parentId;

    /**
     * Items in category if parent
     *
     * @ORM\Column(type="integer")
     */
    private $itemTotal = 0;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSub()
    {
        return $this->sub;
    }

    public function setSub($sub)
    {
        $this->sub = $sub;
    }

    public function getParentId()
    {
        return $this->parentId;
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    public function getItemTotal()
    {
        return $this->itemTotal;
    }

    public function setItemTotal($itemTotal)
    {
        $this->itemTotal = $itemTotal;
    }
}
