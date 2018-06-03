<?php
namespace App\Service;

use App\Entity\Category;
use App\Entity\Listing;
use App\Entity\Shipping;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ListingManager
{
    protected $em;
    protected $tokenStorage;
    protected $status;
    protected $uuid;
    protected $dir;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, $dir)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->dir = $dir;
    }

    /**
     * @param $status string update or create
     */
    public function status($status)
    {
        $this->status = $status;
    }

    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * Checks if required variables are not empty
     *
     * @param $array array variables to check if empty
     * @return bool
     */
    public function verify($array)
    {
        if (empty($this->status)) {
            return false;
        }

        $check = true;
        foreach ($array as $item) {
            if (empty($item)) {
                $check = false;
            }
        }

        return $check;
    }

    /**
     * Updates category of listing
     *
     * @param $category
     * @param $type
     * @param $uuid
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function category($category, $type, $uuid = "")
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $parentCategory = $categoryRepo->findOneById($category);

        $profile = new Profile($this->em, $this->tokenStorage);

        if ($this->status == 'create' && $this->verify([$category, $type])) {
            $uuid = \bin2hex(openssl_random_pseudo_bytes(8));

            $listing = new Listing();
            $listing->setUUID($uuid);
            $listing->setUsername($this->tokenStorage->getToken()->getUser()->getUsername());
            $listing->setTitle('');
            $listing->setFiat($profile->getProfile()->getFiat());
            $listing->setParentCategory($parentCategory->getParentId());
            $listing->setDescription('');
            $listing->setCategory($category);
            $listing->setType($type);
            $listing->setExcludeCountries('');
            $listing->setFromCountry('');
            $listing->setPrice(0);
            $listing->setBTC(0);
            $listing->setXMR(0);
            $listing->setZEC(0);
            $listing->setFlag(true);
            $listing->setFlagReason('Incomplete listing.');

            $this->em->persist($listing);
            $this->em->flush();
            $this->em->clear();

            $this->setUUID($listing->getUUID());
            return $this->getUUID();
        } elseif ($this->status == 'update' && $this->verify([$category, $type, $uuid])) {
            $listingRepo = $this->em->getRepository(Listing::class);
            $listing = $listingRepo->findOneBy([
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
                'uuid' => $uuid
            ]);

            $listing->setParentCategory($parentCategory->getParentId());
            $listing->setCategory($category);
            $listing->setType($type);

            $this->em->merge($listing);
            $this->em->flush();
            $this->em->clear();
            return true;
        }

        return false;
    }

    /**
     * Updates general info of listing
     *
     * @param $title
     * @param $parent
     * @param $uuid
     * @param $stock
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function info($title, $parent, $uuid, $stock)
    {
        if ($this->verify([$title])) {
            $listingRepo = $this->em->getRepository(Listing::class);

            $listing = $listingRepo->findOneBy([
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
                'uuid' => $uuid,
            ]);

            $listing->setTitle($title);
            $listing->setStock($stock);

            if (!is_null($parent)) {
                $listing->setParent($parent);
            }

            if ($this->status == 'create') {
                $listing->setStep(2);
            }

            $this->em->merge($listing);
            $this->em->flush();
            $this->em->clear();
            return true;
        }

        return false;
    }

    /**
     * Updates price of listing and what crypto are accepted
     *
     * @param $price
     * @param $bitcoin
     * @param $monero
     * @param $zcash
     * @param $uuid
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function price($price, $bitcoin, $monero, $zcash, $uuid)
    {
        $cryptocurrency = [$bitcoin, $monero, $zcash];
        if ($this->verify([$price, $cryptocurrency])) {
            $listingRepo = $this->em->getRepository(Listing::class);

            $listing = $listingRepo->findOneBy([
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
                'uuid' => $uuid,
            ]);

            $listing->setPrice($price);
            $listing->setBTC($bitcoin);
            $listing->setXMR($monero);
            $listing->setZEC($zcash);

            if ($this->status == 'create') {
                $listing->setStep(3);
            }

            $this->em->merge($listing);
            $this->em->flush();
            $this->em->clear();
        }

        return false;
    }

    /**
     * Where listing ships from, countries excluded
     *
     * @param $exclude
     * @param $shippingOptions
     * @param $from
     * @param $uuid
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function shipping($exclude, $shippingOptions, $from, $uuid)
    {
        if ($this->verify([$shippingOptions, $from])) {
            $listingRepo = $this->em->getRepository(Listing::class);

            $listing = $listingRepo->findOneBy([
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
                'uuid' => $uuid,
            ]);

            $shippingRepo = $this->em->getRepository(Shipping::class);
            $shipping = $shippingRepo->findByListing($uuid);
            foreach ($shipping as $single) {
                $this->em->remove($single);
            }

            foreach ($shippingOptions as $option) {
                $shipping = new Shipping();

                $shipping->setListing($uuid);
                $shipping->setShippingOption($option);

                $this->em->merge($shipping);
                $this->em->flush();
                $this->em->clear();
            }

            $listing->setExcludeCountries($exclude);
            $listing->setFromCountry($from);

            if ($this->status == 'create') {
                $listing->setStep(4);
            }

            $this->em->merge($listing);
            $this->em->flush();
            $this->em->clear();
            return true;
        }

        return false;
    }

    /**
     * Adds image to listing
     *
     * @param $image
     * @param $uuid
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function image($image, $uuid)
    {
        if ($this->verify([$image, $uuid])) {
            $listingImages = new ListingImages($this->em, $this->tokenStorage, $this->dir);
            $listingImages->setImage($image, $uuid);

            $listingRepo = $this->em->getRepository(Listing::class);

            $listing = $listingRepo->findOneBy([
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
                'uuid' => $uuid,
            ]);

            if ($this->status == 'create') {
                $listing->setStep(5);
            }

            $this->em->merge($listing);
            $this->em->flush();
            $this->em->clear();
            return true;
        }

        return false;
    }

    /**
     * Description of listing
     *
     * @param $description
     * @param $uuid
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function description($description, $uuid)
    {
        if ($this->verify([$description])) {
            $listingRepo = $this->em->getRepository(Listing::class);

            $listing = $listingRepo->findOneBy([
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
                'uuid' => $uuid,
            ]);

            $listing->setDescription($description);

            if ($this->status == 'create') {
                $listing->setStep(6);
            }

            $this->em->merge($listing);
            $this->em->flush();
            $this->em->clear();
            return true;
        }

        return false;
    }

    /**
     * Keywords that make the listing show up in search results
     *
     * @param $keywords
     * @param $uuid
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function keywords($keywords, $uuid)
    {
        if ($this->verify([$keywords])) {
            $listingRepo = $this->em->getRepository(Listing::class);

            $listing = $listingRepo->findOneBy([
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
                'uuid' => $uuid,
            ]);

            $listing->setKeywords($keywords);

            if ($this->status == 'create') {
                $listing->setStep(7);
                $listing->setFlag(false);
            }

            $this->em->merge($listing);
            $this->em->flush();
            $this->em->clear();
            return true;
        }

        return false;
    }

    /**
     * If the product is on sale
     *
     * @param $discount
     * @param $uuid
     * @return bool
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function discount($discount, $uuid)
    {
        if ($this->verify([$discount])) {
            $listingRepo = $this->em->getRepository(Listing::class);

            $listing = $listingRepo->findOneBy([
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
                'uuid' => $uuid,
            ]);

            $listing->setDiscount($discount);

            $this->em->merge($listing);
            $this->em->flush();
            $this->em->clear();
            return true;
        }

        return false;
    }
}
