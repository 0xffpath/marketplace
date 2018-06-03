<?php

namespace App\Controller\Dashboard;

use App\Entity\Wishlist;
use App\Entity\Listing;
use App\Entity\WishlistPublic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WishlistController extends Controller
{
    /**
     * @Route("/wishlist/{uuid}/")
     */
    public function wishlistARAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneByUuid($uuid);

        $wishlistRepo = $em->getRepository(Wishlist::class);
        $wishlist = $wishlistRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'listing' => $uuid]);

        if ($wishlist != null) {
            $em->remove($wishlist);
            $em->flush();
            $em->clear();
            return $this->redirect(urldecode($request->query->get('return')));
        }

        if ($listing != null) {
            $wishlist = new Wishlist();
            $wishlist->setUsername($this->getUser()->getUsername());
            $wishlist->setListing($uuid);
            $em->persist($wishlist);
            $em->flush();
            $em->clear();

            return $this->redirect(urldecode($request->query->get('return')));
        } else {
            throw $this->createNotFoundException('Listing not found.');
        }
    }

    /**
     * @Route("/wishlist/", name="wishlist")
     */
    public function wishlistAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile();

        $wishlistRepo = $em->getRepository(Wishlist::class);
        $wishlistPublicRepo = $em->getRepository(WishlistPublic::class);
        $listingRepo = $em->getRepository(Listing::class);

        $wishlist = $wishlistRepo->findByUsername($this->getUser()->getUsername());
        $wishlistPublic = $wishlistPublicRepo->findOneByUsername($this->getUser()->getUsername());

        foreach ($wishlist as $listing) {
            $listings[] = $listingRepo->findOneByUuid($listing->getListing());
        }
        $link = null;
        if ($wishlistPublic != null) {
            $http = 'http';
            if (isset($_SERVER['HTTPS'])) {
                $http = 'https';
            }

            $link = $http . '://' . $_SERVER['HTTP_HOST'] . "/w/" . $wishlistPublic->getUUID() . '/';
        }

        return $this->render('/dashboard/shared/wishlist.html.twig', [
            'listings' => $listings,
            'url' => $request->getUri(),
            'currency' => $profile->getFiat(),
            'link' => $link,
        ]);
    }

    /**
     * @Route("/wishlist/status/update/", name="wishlistStatus")
     */
    public function wishlistStatusAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $wishlistPublicRepo = $em->getRepository(WishlistPublic::class);
        $wishlistPublic = $wishlistPublicRepo->findOneByUsername($this->getUser()->getUsername());

        if ($wishlistPublic != null) {
            $em->remove($wishlistPublic);
            $em->flush();
            $em->clear();
            return $this->redirect('/wishlist/');
        } else {
            $uuid = \bin2hex(openssl_random_pseudo_bytes(8));

            $wishlistPublic = new WishlistPublic();
            $wishlistPublic->setUsername($this->getUser()->getUsername());
            $wishlistPublic->setUUID($uuid);
            $em->persist($wishlistPublic);
            $em->flush();
            $em->clear();
        }

        return $this->redirect('/wishlist/');
    }

    /**
     * @Route("/w/{uuid}/", name="wishlistPublic")
     */
    public function wishlistPublicAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile();

        $wishlistRepo = $em->getRepository(Wishlist::class);
        $wishlistPublicRepo = $em->getRepository(WishlistPublic::class);
        $wishlistPublic = $wishlistPublicRepo->findOneByUuid($uuid);

        $listingRepo = $em->getRepository(Listing::class);

        $wishlist = $wishlistRepo->findByUsername($wishlistPublic->getUsername());

        $listings = [];
        foreach ($wishlist as $listing) {
            $listings[] = $listingRepo->findOneById($listing->getListing());
        }

        $link = null;
        if ($wishlistPublic != null) {
            $http = 'http';
            if (isset($_SERVER['HTTPS'])) {
                $http = 'https';
            }

            $link = $http . '://' . $_SERVER['HTTP_HOST'] . "/w/" . $wishlistPublic->getUUID() . '/';
        }

        return $this->render('/wishlist.html.twig', [
            'listings' => $listings,
            'url' => $request->getUri(),
            'currency' => $profile->getFiat(),
            'link' => $link,
            'uuid' => $wishlistPublic->getUUID(),
        ]);
    }
}
