<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Wishlist
{
    protected $em;
    private $tokenStorage;

    public function __construct(EntityManager $em, TokenStorageInterface $securityToken)
    {
        $this->em = $em;
        $this->tokenStorage = $securityToken;
    }

    /**
     * Return list of wishlist listings
     *
     * @return array
     */
    public function getList()
    {
        $wishlist = [];
        if ($this->tokenStorage->getToken()->getUser() != 'anon.') {
            $wishlistRepo = $this->em->getRepository(\App\Entity\Wishlist::class);
            $wishlist = $wishlistRepo->createQueryBuilder('r')
                ->select('r.listing')
                ->where('r.username = :username')
                ->setParameter('username', $this->tokenStorage->getToken()->getUser()->getUsername())
                ->getQuery()
                ->getArrayResult();
        }
        $data = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($wishlist));
        $wishlist = [];
        foreach ($data as $v) {
            $wishlist[] = $v;
        }

        return $wishlist;
    }

    /**
     * Returns if listings are favorites by filtering through them
     *
     * @param $listings array array of listings
     * @param $username string username to get wishlist
     * @return array
     */
    public function getWishlistListings($listings, $username)
    {
        //if logged in, filter
        if ($username != null) {
            $wishlist = $this->getList();
            $wishlistListings = [];
            //if the id of listing in list of favorites, add to listings
            if (!empty($listings)) {
                foreach ($listings as $listing) {
                    if (in_array($listing['uuid'], $wishlist)) {
                        $listing['wishlist'] = true;
                    } else {
                        $listing['wishlist'] = false;
                    }
                    $wishlistListings[] = $listing;
                }
            }

            return $wishlistListings;
        } else {
            return $listings;
        }
    }
}
