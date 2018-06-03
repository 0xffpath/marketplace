<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends Controller
{

    /**
     * @Route("/home/", name="home")
     */
    public function homeAction(Request $request, $cat = '')
    {
        $em = $this->getDoctrine()->getManager();

        $recentVendors = $em->createQuery('select distinct vp from App:VendorProfile vp left join App:Listing l with vp.username = l.username order by vp.id desc')
            ->setMaxResults(4)
            ->getArrayResult();

        $topVendors = $em->createQuery('select distinct vp from App:VendorProfile vp left join App:Listing l with vp.username = l.username where vp.positive  > 0 order by vp.positive desc')
            ->setMaxResults(4)
            ->getArrayResult();

        if ($this->getUser() !== null) {
            if ($this->getUser()->getRole() === 'new_vendor') {
                return new RedirectResponse($this->generateUrl('vendor'));
            }
            if ($this->getUser()->getRole() === 'vendor') {
                return new RedirectResponse($this->generateUrl('dashboard'));
            }
            if ($this->getUser()->getRole() === 'admin') {
                return new RedirectResponse($this->generateUrl('staffDashboard'));
            }
        }

        //hot items for past 5 days.
        $orders = $em->createQuery('select count(o.listing) as vo, l as listing, li.image from App:Orders o inner join App:ListingImages li with li.listing = o.listing  inner join App:Listing l with o.listing = l.uuid group by o.listing order by vo desc')
            ->setMaxResults(8)
            ->getArrayResult();

        $hotProducts = [];
        $count = 0;
        foreach ($orders as $order) {
            $same = false;
            for ($i=0;$i<$count;$i++) {
                if ($hotProducts[$i]['id'] == $order['listing']['id']) {
                    $same = true;
                }
            }
            if (!$same) {
                $hotProducts[$count] = $order['listing'];
                $hotProducts[$count]['image'] = $order['image'];
                $count++;
            }
        }

        $staffPicks = [];

        //staff picks
        $picks = $em->createQuery('select l as listing, li.image from App:StaffPick sp inner join App:ListingImages li with li.listing = sp.listing inner join App:Listing l with sp.listing = l.uuid')
            ->setMaxResults(12)
            ->getArrayResult();
        $count = 0;
        foreach ($picks as $pick) {
            $same = false;
            for ($i=0;$i<$count;$i++) {
                if ($staffPicks[$i]['id'] == $pick['listing']['id']) {
                    $same = true;
                }
            }
            if (!$same) {
                $staffPicks[$count] = $pick['listing'];
                $staffPicks[$count]['image'] = $pick['image'];
                $count++;
            }
        }

        $listings = $em->createQuery('select l as listing, li.image as image from App:Listing l join App:ListingImages li with li.listing = l.uuid GROUP BY l.id ORDER BY l.id DESC')
            ->setMaxResults(12)
            ->getArrayResult();

        $newProducts = [];
        $count = 0;
        foreach ($listings as $listing) {
            $same = false;
            for ($i=0;$i<$count;$i++) {
                if ($newProducts[$i]['id'] == $listing['listing']['id']) {
                    $same = true;
                }
            }
            if (!$same) {
                $newProducts[$count] = $listing['listing'];
                $newProducts[$count]['image'] = $listing['image'];
                $count++;
            }
        }

        return $this->render('/home/home.html.twig', [
            'cat' => $request->query->get('cat'),
            'url' => 'home',
            'recentVendors' => $recentVendors,
            'topVendors' => $topVendors,
            'hotProducts' => $hotProducts,
            'newProducts' => $newProducts,
            'staffPicks' => $staffPicks,
        ]);
    }
}
