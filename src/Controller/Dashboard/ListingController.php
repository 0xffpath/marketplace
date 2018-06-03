<?php

namespace App\Controller\Dashboard;

use App\Entity\Listing;
use App\Entity\Shipping;
use App\Entity\ShippingOption;
use App\Entity\VendorProfile;
use App\Form\Vendor\Listing\CategoryType;
use App\Form\Vendor\Listing\DescriptionType;
use App\Form\Vendor\Listing\DiscountType;
use App\Form\Vendor\Listing\ImageType;
use App\Form\Vendor\Listing\InfoType;
use App\Form\Vendor\Listing\PricingType;
use App\Form\Vendor\Listing\SearchType;
use App\Form\Vendor\Listing\ShippingType;
use App\Form\Vendor\TacType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ListingController extends Controller
{

    /**
     * @Route("/listings/", name="listings")
     */
    public function listingsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile();

        $search = $request->query->get('search');

        $listingRepo = $em->getRepository(Listing::class);
        $listings = $listingRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.username = :username and (r.title like :title or r.id = :id)')
            ->orderBy('r.id', 'DESC')
            ->setParameters(['username' => $this->getUser()->getUsername(), 'title' => '%' . $search . '%', 'id' => $search])
            ->getQuery()
            ->getArrayResult();

        return $this->render('/dashboard/vendor/listing/listings.html.twig', [
            'listings' => $listings,
            'tac' => $profile->getTac(),
        ]);
    }

    /**
     * @Route("/listing/new/", name="newListing")
     */
    public function listingsNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //check if disabled
        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }

        return $this->redirect('/listing/handle/new/');
    }

    /**
     * @Route("/listing/handle/category/{uuid}/", name="handleListingCategoryEdit")
     * @Route("/listing/handle/new/", name="handleListingCategory")
     */
    public function listingsHandleCategoryAction(Request $request, $uuid = "")
    {
        $em = $this->getDoctrine()->getManager();
        $session = new Session();

        $listing = [];
        if (!empty($uuid)) {
            $listingRepo = $em->getRepository(Listing::class);
            $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);
        }

        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }

        $allCategories = $this->get('App\Service\Categories')->getAll();

        $cat = [];
        //puts categories into [title, id] format
        $cat[''] = ['' => ''];
        foreach ($allCategories as $categories) {
            $head = $categories[0];
            $sub = $categories[1];
            $subcat = [];
            foreach ($sub as $category) {
                $subcat[$category->getTitle()] = $category->getId();
            }
            $cat[$head['title']] = $subcat;
        }

        $step = 1;

        if (empty($uuid) && empty($listing)) {
            $form = $this->createForm(CategoryType::class, [], [
                'categories' => $cat,
            ]);
        } elseif (!empty($uuid) && !empty($listing)) {
            $form = $this->createForm(CategoryType::class, [], [
                'categories' => $cat,
                'category' => $listing->getCategory(),
                'type' => $listing->getType(),
            ]);
            $step = $listing->getStep();
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->get('type')->getData();
            $category = $form->get('category')->getData();

            $lm = $this->get('App\Service\ListingManager');

            if (empty($uuid)) {
                $lm->status('create');
            } else {
                $lm->status('update');
            }

            if (!empty($uuid) && $lm->category($category, $type, $uuid)) {
                return $this->redirect('/listing/handle/info/' . $uuid  . '/');
            } elseif (!empty($uuid)) {
                $session->getFlashBag()->set('listingError', 'You must enter a category.');
            }

            if (empty($uuid) && $lm->category($category, $type)) {
                $uuid = $lm->getUUID();

                return $this->redirect('/listing/handle/info/' . $uuid  . '/');
            } elseif (empty($uuid)) {
                $session->getFlashBag()->set('listingError', 'You must enter a category.');
            }
        }

        return $this->render('/dashboard/vendor/listing/handle/category.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
            'step' => $step,
        ]);
    }

    /**
     * @Route("/listing/handle/info/{uuid}/", name="handleListingInfo")
     */
    public function listingsHandleInfoAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }

        $listingRepo = $em->getRepository(Listing::class);

        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);

        $listings = $listingRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.username = :username and r.parent = 0 and r.id != :id')
            ->setParameters([
                'username' => $this->getUser()->getUsername(),
                'id' => $listing->getId(),
            ])
            ->getQuery()
            ->getResult();


        $selected = [];
        foreach ($listings as $single) {
            $parent[$single->getTitle()] = $single->getUUID();
            if ($single->getUUID() == $listing->getParent()) {
                $selected[$single->getTitle()] = ['selected' => 'selected'];
            }
        }

        $form = $this->createForm(InfoType::class, [], [
            'title' => $listing->getTitle(),
            'parent' => $parent,
            'selected' => $selected,
            'stock' => $listing->getStock(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form->get('title')->getData();
            $parent = $form->get('parent')->getData();
            $stock = $form->get('stock')->getData();

            $lm = $this->get('App\Service\ListingManager');
            if ($listing->getStep() >= 2) {
                $lm->status('update');
            } elseif ($listing->getStep() == 1) {
                $lm->status('create');
            }
            $lm->info($title, $parent, $uuid, $stock);

            return $this->redirect('/listing/handle/pricing/' . $uuid  . '/');
        }

        $profile = $this->get('App\Service\Profile')->getProfile();
        return $this->render('/dashboard/vendor/listing/handle/info.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
            'profile' => $profile,
            'step' => $listing->getStep(),
        ]);
    }

    /**
     * @Route("/listing/handle/pricing/{uuid}/", name="handleListingPricing")
     */
    public function listingsHandlePricingAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }

        $listingRepo = $em->getRepository(Listing::class);

        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);

        if ($listing->getStep() < 2) {
            return $this->redirect('/listing/handle/info/' . $uuid . '/');
        }

        $form = $this->createForm(PricingType::class, [], [
            'price' => $listing->getPrice(),
            'btc' => $listing->getBTC(),
            'xmr' => $listing->getXMR(),
            'zec' => $listing->getZEC(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $price = $form->get('price')->getData();

            $bitcoin = $form->get('btc')->getData();
            $monero = $form->get('xmr')->getData();
            $zcash = $form->get('zec')->getData();

            $lm = $this->get('App\Service\ListingManager');
            if ($listing->getStep() >= 3) {
                $lm->status('update');
            } elseif ($listing->getStep() == 2) {
                $lm->status('create');
            }
            $lm->price($price, $bitcoin, $monero, $zcash, $uuid);

            return $this->redirect('/listing/handle/shipping/' . $uuid  . '/');
        }

        $profile = $this->get('App\Service\Profile')->getProfile();
        return $this->render('/dashboard/vendor/listing/handle/pricing.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
            'profile' => $profile,
            'step' => $listing->getStep(),
            'bitcoin' => $profile->getBTCPublic(),
            'monero' => $profile->getXMRAddress(),
            'zcash' => $profile->getZECAddress(),
        ]);
    }

    /**
     * @Route("/listing/handle/shipping/{uuid}/", name="handleListingShipping")
     */
    public function listingsHandleShippingAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }

        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);

        if ($listing->getStep() < 3) {
            return $this->redirect('/listing/handle/pricing/' . $uuid . '/');
        }

        $shippingRepo = $em->getRepository(ShippingOption::class);
        $shippingOption = $shippingRepo->createQueryBuilder('r')
            ->select('r.id, r.shippingOption, r.price')
            ->where('r.username = :username')
            ->setParameter('username', $this->getUser()->getUsername())
            ->getQuery()
            ->getArrayResult();

        $allCategories = $this->get('App\Service\Categories')->getAll();

        $cat = [];
        //put in order
        foreach ($allCategories as $categories) {
            $head = $categories[0];
            $sub = $categories[1];
            $subcat = [];
            foreach ($sub as $category) {
                $subcat[$category->getTitle()] = $category->getId();
            }
            $cat[$head['title']] = $subcat;
        }

        $selectedShippingRepo = $em->getRepository(Shipping::class);
        $selectedShipping = $selectedShippingRepo->createQueryBuilder('r')
            ->select('r.shippingOption as id')
            ->where('r.listing = :listing')
            ->setParameter('listing', $uuid)
            ->getQuery()
            ->getArrayResult();


        $options = [];
        $selected = [];

        //find which are selected
        foreach ($shippingOption as $shipping) {
            $options[$shipping['shippingOption'] . ' :: ' . $shipping['price']] = $shipping['id'];
            if (in_array($shipping['id'], array_column($selectedShipping, 'id'))) {
                $selected[$shipping['shippingOption'] . ' :: ' . $shipping['price']] = ['selected' => 'selected'];
            }
        }


        $option['shipping'] = $options;


        $countryList = $this->get('App\Service\Countries')->getCountries();
        $excludeSelected = [];

        $excludeCountries = explode(', ', $listing->getExcludeCountries());

        $countryContinentList = $this->get('App\Service\Countries')->getCountriesByContinent();

        $excludeSelected['southAmerica'] = [];
        $excludeSelected['asia'] = [];
        $excludeSelected['africa'] = [];
        $excludeSelected['australia'] = [];
        $excludeSelected['europe'] = [];
        $excludeSelected['northAmerica'] = [];

        foreach ($excludeCountries as $country) {
            foreach ($countryContinentList as $key => $continent) {
                if (\in_array($country, $continent)) {
                    $excludeSelected[$key][$country] = ['selected' => 'selected'];
                }
            }
        }

        $countries = [];
        foreach ($countryList as $country) {
            $countries[$country] = $country;
        }

        $form = $this->createForm(ShippingType::class, [], [
            'shippingOptions' => $option,
            'selectedShipping' => $selected,
            'fromOptions' => $countries,
            'from' => $listing->getFromCountry(),
            'southAmericaOptions' => $countryContinentList['southAmerica'],
            'asiaOptions' => $countryContinentList['asia'],
            'africaOptions' => $countryContinentList['africa'],
            'australiaOptions' => $countryContinentList['australia'],
            'europeOptions' => $countryContinentList['europe'],
            'northAmericaOptions' => $countryContinentList['northAmerica'],
            'southAmericaSelected' => $excludeSelected['southAmerica'],
            'asiaSelected' => $excludeSelected['asia'],
            'africaSelected' => $excludeSelected['africa'],
            'australiaSelected' => $excludeSelected['australia'],
            'europeSelected' => $excludeSelected['europe'],
            'northAmericaSelected' => $excludeSelected['northAmerica'],
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $shippingOptions = $form->get('shipping')->getData();
            $from = $form->get('from')->getData();

            $countryOptions['southAmerica'] = $form->get('southAmerica')->getData();
            $countryOptions['asia'] = $form->get('asia')->getData();
            $countryOptions['africa'] = $form->get('africa')->getData();
            $countryOptions['europe'] = $form->get('europe')->getData();
            $countryOptions['australia'] = $form->get('australia')->getData();
            $countryOptions['northAmerica'] = $form->get('northAmerica')->getData();

            //add check boxes
            $countryAll['southAmerica'] = $form->get('southAmericaAll')->getData();
            $countryAll['asia'] = $form->get('asiaAll')->getData();
            $countryAll['africa'] = $form->get('africaAll')->getData();
            $countryAll['europe'] = $form->get('europeAll')->getData();
            $countryAll['australia'] = $form->get('australiaAll')->getData();
            $countryAll['northAmerica'] = $form->get('northAmericaAll')->getData();

            $exclude = '';
            foreach ($countryAll as $key => $continent) {
                if ($continent != "") {
                    foreach ($countryContinentList[$key] as $country) {
                        $exclude .= $country . ", ";
                    }
                } else {
                    foreach ($countryOptions[$key] as $country) {
                        $exclude .= $country . ", ";
                    }
                }
            }

            $exclude = \rtrim($exclude, ", ");

            $lm = $this->get('App\Service\ListingManager');
            if ($listing->getStep() >= 4) {
                $lm->status('update');
            } elseif ($listing->getStep() == 3) {
                $lm->status('create');
            }
            $lm->shipping($exclude, $shippingOptions, $from, $uuid);

            return $this->redirect('/listing/handle/images/' . $uuid  . '/');
        }

        $profile = $this->get('App\Service\Profile')->getProfile();
        return $this->render('/dashboard/vendor/listing/handle/shipping.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
            'profile' => $profile,
            'step' => $listing->getStep(),
        ]);
    }


    /**
     * @Route("/listing/handle/images/{uuid}/", name="handleListingImages")
     */
    public function listingsHandleImageAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }


        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);

        if ($listing->getStep() < 4) {
            return $this->redirect('/listing/handle/shipping/' . $uuid . '/');
        }

        $form = $this->createForm(ImageType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            $lm = $this->get('App\Service\ListingManager');
            if ($listing->getStep() >= 5) {
                $lm->status('update');
            } elseif ($listing->getStep() == 4) {
                $lm->status('create');
            }
            $lm->image($image, $uuid);

            return $this->redirect('/listing/handle/images/' . $uuid  . '/');
        }

        $profile = $this->get('App\Service\Profile')->getProfile();
        return $this->render('/dashboard/vendor/listing/handle/images.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
            'profile' => $profile,
            'step' => $listing->getStep(),
        ]);
    }

    /**
     * @Route("/listing/handle/description/{uuid}/", name="handleListingDescription")
     */
    public function listingsHandleDescriptionAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }

        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);

        if ($listing->getStep() < 5) {
            return $this->redirect('/listing/handle/pricing/' . $uuid . '/');
        }

        $form = $this->createForm(DescriptionType::class, [], [
            'description' => $listing->getDescription(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $description = $form->get('description')->getData();

            $lm = $this->get('App\Service\ListingManager');
            if ($listing->getStep() >= 6) {
                $lm->status('update');
            } elseif ($listing->getStep() == 5) {
                $lm->status('create');
            }
            $lm->description($description, $uuid);

            return $this->redirect('/listing/handle/search/' . $uuid  . '/');
        }

        $profile = $this->get('App\Service\Profile')->getProfile();
        return $this->render('/dashboard/vendor/listing/handle/description.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
            'profile' => $profile,
            'step' => $listing->getStep(),
        ]);
    }

    /**
     * @Route("/listing/handle/search/{uuid}/", name="handleListingSearch")
     */
    public function listingsHandleSearchAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }

        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);

        if ($listing->getStep() < 6) {
            return $this->redirect('/listing/handle/pricing/' . $uuid . '/');
        }

        $form = $this->createForm(SearchType::class, [], [
            'keywords' => $listing->getKeywords(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $keywords = $form->get('keywords')->getData();

            $lm = $this->get('App\Service\ListingManager');
            if ($listing->getStep() >= 7) {
                $lm->status('update');
            } elseif ($listing->getStep() == 6) {
                $lm->status('create');
            }
            $add = $lm->keywords($keywords, $uuid);

            //if finished, add to category total
            if ($add) {
                $this->get('App\Service\Categories')->addItem($listing->getCategory());
            }

            return $this->redirect('/listing/handle/search/' . $uuid  . '/');
        }

        $profile = $this->get('App\Service\Profile')->getProfile();
        return $this->render('/dashboard/vendor/listing/handle/search.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
            'profile' => $profile,
            'step' => $listing->getStep(),
        ]);
    }

    /**
     * @Route("/listing/handle/discount/{uuid}/", name="handleListingDiscount")
     */
    public function listingsHandleDiscountAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $disabled = $this->get('App\Service\ListingDisable')->check();
        if ($disabled) {
            return $this->render('/dashboard/vendor/listing/disable.html.twig');
        }

        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);

        if ($listing->getStep() < 7) {
            return $this->redirect('/listing/handle/pricing/' . $uuid . '/');
        }

        $form = $this->createForm(DiscountType::class, [], [
            'discount' => $listing->getDiscount(),
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $discount = $form->get('discount')->getData();

            $lm = $this->get('App\Service\ListingManager');
            $lm->status('update');
            $lm->discount($discount, $uuid);

            return $this->redirect('/listing/handle/discount/' . $uuid  . '/');
        }

        $profile = $this->get('App\Service\Profile')->getProfile();
        return $this->render('/dashboard/vendor/listing/handle/discount.html.twig', [
            'disabled' => $disabled,
            'form' => $form->createView(),
            'profile' => $profile,
            'step' => $listing->getStep(),
        ]);
    }


    /**
     * @Route("/listing/edit/{uuid}/", name="editListing")
     */
    public function listingsEditAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);

        switch ($listing->getStep()) {
            case 1:
                return $this->redirect('/listing/handle/category/' . $uuid . '/');
                break;
            case 2:
                return $this->redirect('/listing/handle/info/' . $uuid . '/');
                break;
            case 3:
                return $this->redirect('/listing/handle/pricing/' . $uuid . '/');
                break;
            case 4:
                return $this->redirect('/listing/handle/shipping/' . $uuid . '/');
                break;
            case 5:
                return $this->redirect('/listing/handle/images/' . $uuid . '/');
                break;
            case 6:
                return $this->redirect('/listing/handle/description/' . $uuid . '/');
                break;
            case 7:
                return $this->redirect('/listing/handle/search/' . $uuid . '/');
                break;
            default:
                return $this->redirect('/listing/handle/category/' . $uuid . '/');
                break;
        }
    }

    /**
     * @Route("/listing/delete/{uuid}/", name="deleteListing")
     */
    public function listingDeleteAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $listingRepo = $em->getRepository(Listing::class);
        $listing = $listingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'uuid' => $uuid]);
        $categoryId = $listing->getCategory();
        $parentId = $listing->getParentCategory();

        $em->remove($listing);
        $em->flush();

        $shippingRepo = $em->getRepository(Shipping::class);
        $shipping = $shippingRepo->findByListing($uuid);
        foreach ($shipping as $single) {
            $em->remove($single);
        }
        $em->flush();
        $em->clear();

        if ($listing->getStep() == 7) {
            $this->get('App\Service\Categories')->removeItem($categoryId);
            $this->get('App\Service\Categories')->removeItem($parentId);
        }

        $this->get('App\Service\ListingImages')->removeImages($uuid, $this->getUser()->getUsername());

        return $this->redirect('/listings/');
    }

    /**
     * @Route("/listings/tac/", name="tacListing")
     */
    public function listingsTacAction(Request $request)
    {
        $routeName = $this->container->get('request_stack')->getMasterRequest()->get('_route');

        $em = $this->getDoctrine()->getManager();

        $profileRepo = $em->getRepository(VendorProfile::class);
        $profile = $profileRepo->findOneByUsername($this->getUser()->getUsername());

        $tacForm = $this->createForm(TacType::class, [], ['tac' => $profile->getTac()]);
        $tacForm->handleRequest($request);

        if ($tacForm->isSubmitted() && $tacForm->isValid()) {
            $profile->setTac($tacForm->get('tac')->getData());
            $em->merge($profile);
            $em->flush();
            $em->clear();
        }

        return $this->render('/dashboard/vendor/tac.html.twig', [
            'route' => $routeName,
            'tacForm' => $tacForm->createView()
        ]);
    }
}
