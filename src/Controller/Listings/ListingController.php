<?php

namespace App\Controller\Listings;

use App\Entity\Feedback;
use App\Entity\Wishlist;
use App\Entity\Listing;
use App\Entity\Shipping;
use App\Entity\ShippingOption;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ListingController extends Controller
{
    /**
     * @Route("/l/{uuid}/", name="product")
     */
    public function listingAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneByUuid($uuid);

        $profile = $this->get('App\Service\Profile')->getProfile($listing->getUsername());

        if ($listing == null) {
            throw $this->createNotFoundException('This listing was not found.');
        }

        $wishlist = false;
        if ($this->getUser() != null) {
            $wishlistRepo = $em->getRepository(Wishlist::class);
            $wishlist = $wishlistRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'listing' => $uuid]);
            if ($wishlist != null) {
                $wishlist = true;
            } else {
                $wishlist = false;
            }
        }

        $shippingRepo = $em->getRepository(Shipping::class);
        $shippingOptionRepo = $em->getRepository(ShippingOption::class);

        $shipping = $shippingRepo->findByListing($uuid);
        $shippingOptions = [];
        foreach ($shipping as $option) {
            $shippingOptions[] = $shippingOptionRepo->findOneById($option->getShippingOption());
        }

        $offset = $request->query->get('offset');
        $count = $this->get('App\Service\ListingImages')->getImageCount($uuid);
        if (!is_numeric($offset) || $count-1 < $offset) {
            $offset = 0;
        }

        $subs = [];
        if ($listing->getParent() == 0) {
            $subs = $em->createQuery('select l as listing, li.image as image from App:Listing l join App:ListingImages li with li.listing = l.uuid where l.parent = :uuid or l.uuid = :uuid GROUP BY l.id')
                ->setMaxResults(8)
                ->setParameter('uuid', $uuid)
                ->getArrayResult();
        } else {
            $subs = $em->createQuery('select l as listing, li.image as image from App:Listing l join App:ListingImages li with li.listing = l.uuid where l.parent = :uuid or l.uuid = :uuid GROUP BY l.id')
                ->setMaxResults(8)
                ->setParameter('uuid', $listing->getParent())
                ->getArrayResult();
        }

        $feedbackRepo = $em->getRepository(Feedback::class);
        $feedback = $feedbackRepo->findByListing($uuid);


        return $this->render('/listing.html.twig', [
            'shipping' => $shippingOptions,
            'listing' => $listing,
            'subs' => $subs,
            'url' => $request->getUri(),
            'wishlist' => $wishlist,
            'feedback' => $feedback,
            'offset' => $offset,
            'count' => $count,
            'profile' => $profile,
        ]);
    }
}
