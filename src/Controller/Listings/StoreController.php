<?php

namespace App\Controller\Listings;

use App\Entity\Listing;
use App\Entity\VendorProfile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StoreController extends Controller
{
    /**
     * @Route("/store/{username}/", name="store")
     */
    public function storeAction(Request $request, $username)
    {
        $em = $this->getDoctrine()->getManager();

        $profileRepo = $em->getRepository(VendorProfile::class);
        $profile = $profileRepo->findOneByUsername($username);

        if ($profile == null) {
            throw $this->createNotFoundException('This vendor was not found.');
        }

        $listingRepo = $em->getRepository(Listing::class);

        $listings = $listingRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.username = :username and r.flag = :flag')
            ->setParameters(['username' => $username, 'flag' => 0])
            ->getQuery()
            ->getArrayResult();

        $listings = $this->get('App\Service\Wishlist')->getWishlistListings($listings, $this->getUser());

        return $this->render('/store.html.twig', [
            'url' => $request->getUri(),
            'cat' => $request->query->get('cat'),
            'profile' => $profile,
            'listings' => $listings,
            'subactive' => $request->query->get('sub'),
        ]);
    }
}
