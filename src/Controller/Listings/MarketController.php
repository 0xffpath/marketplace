<?php

namespace App\Controller\Listings;

use App\Form\FilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MarketController extends Controller
{

    /**
     * @Route("/market/", name="market")
     */
    public function marketAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $search = $request->query->get('search');

        //if user didn't search
        if ($search == null) {
            $search = "";
        }

        $page = 1;
        if (is_numeric($request->query->get('page'))) {
            $page = $request->query->get('page');
        }

        $category = $request->query->get('cat');

        //change to sub if exists
        if ($request->query->get('sub') != null) {
            $category = $request->query->get('sub');
        }

        //build query based on filter
        $query = "";

        if ($category != null) {
            $query .= " and (l.category = :category or l.parentCategory = :category)";
        }

        if ($request->query->get('verified') == 1) {
            $query .= " and vp.verified = 1";
        }

        if ($request->query->get('active') == 1) {
            $query .= " and vp.lastSeen > :time";
        }

        if ($request->query->get('positive')) {
            $query .= " and (vp.totalFeedback = 0 or ((vp.positive+(vp.neutral*0.8))/vp.totalFeedback) >= 0.80)";
        }

        if ($request->query->get('physical') == 1 && $request->query->get('digital') == 1) {
            $query .= " and (l.type = 'physical' or l.type = 'digital')";
        }

        if ($request->query->get('physical') == 0 && $request->query->get('digital') == 1) {
            $query .= " and l.type = 'digital'";
        }

        if ($request->query->get('physical') == 1 && $request->query->get('digital') == 0) {
            $query .= " and l.type = 'physical'";
        }

        if ($request->query->get('min') != null && is_numeric($request->query->get('min'))) {
            $query .= " and (l.price - l.discount) >= :min";
        }

        if ($request->query->get('max') != null && is_numeric($request->query->get('max'))) {
            $query .= " and (l.price - l.discount) <= :max";
        }

        if ($request->query->get('level') != null && filter_var($request->query->get('level'), FILTER_VALIDATE_INT)) {
            $query .= " and vp.level >= :level";
        }

        $btc = $request->query->get('btc') ;
        $xmr = $request->query->get('xmr') ;
        $zec = $request->query->get('zec') ;

        switch (true) {
            case $btc == 1 && $xmr == 1 && $zec == 1:
                $query .= " and (l.btc = 1 or l.xmr = 1 or l.zec = 1)";
                break;
            case $btc == null && $xmr == 1 && $zec == 1:
                $query .= " and (l.xmr = 1 or l.zec = 1)";
                break;
            case $btc == null && $xmr == null && $zec == 1:
                $query .= " and l.zec = 1";
                break;
            case $btc == 1 && $xmr == null && $zec == 1:
                $query .= " and (l.btc = 1 or l.zec = 1)";
                break;
            case $btc == 1 && $xmr == null && $zec == null:
                $query .= " and l.btc = 1";
                break;
            case $btc == 1 && $xmr == 1 && $zec == null:
                $query .= " and (l.btc = 1 or l.xmr = 1)";
                break;
        }

        $totalPages = $em->createQuery("select count(l) from App:Listing l inner join App:VendorProfile vp with l.username = vp.username where (l.title like :search or l.keywords like :search) $query");


        //set the parameters based on the search
        if ($category == null) {
            $totalPages->setParameter('search', '%' . $search . '%');
        } else {
            $totalPages->setParameters(['category' => $category, 'search' => '%' . $search . '%']);
        }

        if ($request->query->get('active') != null) {
            $totalPages->setParameter('time', time() + 259200);
        }

        if ($request->query->get('min') != null && is_numeric($request->query->get('min'))) {
            $totalPages->setParameter('min', $request->query->get('min'));
        }

        if ($request->query->get('max') != null && is_numeric($request->query->get('max'))) {
            $totalPages->setParameter('max', $request->query->get('max'));
        }

        if ($request->query->get('level') != null && filter_var($request->query->get('level'), FILTER_VALIDATE_INT)) {
            $totalPages->setParameter('level', $request->query->get('level'));
        }

        $totalPages = $totalPages->getSingleScalarResult();

        $totalPages = ceil($totalPages/10);

        $data = $em->createQuery("select l, (select vp1.verified from App:VendorProfile vp1 where vp1.username = l.username), (select vp2.positive from App:VendorProfile vp2 where vp2.username = l.username), (select vp3.negative from App:VendorProfile vp3 where vp3.username = l.username), (select vp4.level from App:VendorProfile vp4 where vp4.username = l.username) from App:Listing l inner join App:VendorProfile vp with l.username = vp.username where (l.title like :search or l.keywords like :search) $query")
            ->setMaxResults(10)
            ->setFirstResult(($page*10)-10);

        if ($category == null) {
            $data->setParameters(['search' => '%' . $search . '%']);
        } else {
            $data->setParameters(['category' => $category, 'search' => '%' . $search . '%']);
        }

        if ($request->query->get('active') != null) {
            $data->setParameter('time', time() - 259200);
        }

        if ($request->query->get('min') != null && is_numeric($request->query->get('min'))) {
            $data->setParameter('min', $request->query->get('min'));
        }

        if ($request->query->get('max') != null && is_numeric($request->query->get('max'))) {
            $data->setParameter('max', $request->query->get('max'));
        }

        if ($request->query->get('level') != null && filter_var($request->query->get('level'), FILTER_VALIDATE_INT)) {
            $data->setParameter('level', $request->query->get('level'));
        }

        $data = $data->getArrayResult();

        $listings = [];

        //breakup subquery
        $count = 0;
        foreach ($data as $listing) {
            $listings[] = $listing[0];
            $listings[$count]['verified'] = $listing[1];
            $listings[$count]['positive'] = $listing[2];
            $listings[$count]['negative'] = $listing[3];
            $listings[$count]['level'] = $listing[4];
            $count++;
        }


        //form to filter search results
        $filterForm = $this->createForm(FilterType::class);

        //alter listings to include if the listing is a favorite
        $listings = $this->get('App\Service\Wishlist')->getWishlistListings($listings, $this->getUser());

        return $this->render('/market.html.twig', [
            'title' => $search,
            'filterForm' => $filterForm->createView(),
            'listings' => $listings,
            'totalPages' => $totalPages,
            'page' => $page,
        ]);
    }
}
