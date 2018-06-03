<?php

namespace App\Controller\Dashboard;

use App\Entity\ShippingOption;
use App\Form\Vendor\ShippingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ShippingController extends Controller
{

    /**
     * @Route("/shipping/", name="shipping")
     */
    public function shippingAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $shippingRepo = $em->getRepository(ShippingOption::class);
        $options = $shippingRepo->findByUsername($this->getUser()->getUsername());

        $profile = $this->get('App\Service\Profile')->getProfile();

        return $this->render('/dashboard/vendor/shipping.html.twig', [
            'options' => $options,
            'currency' => $profile->getFiat()
        ]);
    }

    /**
     * @Route("/shipping/new/", name="newShipping")
     */
    public function shippingNewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile();

        $shippingForm = $this->createForm(ShippingType::class);
        $shippingForm->handleRequest($request);

        if ($shippingForm->isSubmitted() && $shippingForm->isValid()) {
            $option = $shippingForm->get('option')->getData();
            $price = $shippingForm->get('price')->getData();

            $shippingOption = new ShippingOption();
            $shippingOption->setUsername($this->getUser()->getUsername());
            $shippingOption->setShippingOption($option);
            $shippingOption->setPrice(number_format($price, 2));
            $shippingOption->setFiat($profile->getFiat());
            $em->persist($shippingOption);
            $em->flush();
            $em->clear();

            return $this->redirect('/shipping/');
        }


        return $this->render('/dashboard/vendor/newshipping.html.twig', [
            'shippingForm' => $shippingForm->createView(),
            'currency' => $profile->getFiat()
        ]);
    }

    /**
     * @Route("/shipping/delete/{id}/", name="deleteShipping")
     */
    public function shippingDeleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $shippingRepo = $em->getRepository(ShippingOption::class);
        $shippingOption = $shippingRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'id' => $id]);
        $em->remove($shippingOption);
        $em->flush();
        return $this->redirect('/shipping/');
    }
}
