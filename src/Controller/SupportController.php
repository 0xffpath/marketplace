<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SupportController extends Controller
{

    /**
     * @Route("/support/", name="support")
     */
    public function supportAction(Request $request)
    {
        return $this->render('/support/support.html.twig');
    }

    /**
     * @Route("/support/crypto/", name="supportCrypto")
     */
    public function cryptoAction(Request $request)
    {
        return $this->render('/support/pages/crypto.html.twig');
    }

    /**
     * @Route("/support/ordering/", name="supportOrdering")
     */
    public function orderingAction(Request $request)
    {
        return $this->render('/support/pages/ordering.html.twig');
    }

    /**
     * @Route("/support/finalizing/", name="supportFinalizing")
     */
    public function finalizingAction(Request $request)
    {
        return $this->render('/support/pages/finalizing.html.twig');
    }

    /**
     * @Route("/support/disputes/", name="supportDisputes")
     */
    public function disputesAction(Request $request)
    {
        return $this->render('/support/pages/disputes.html.twig');
    }

    /**
     * @Route("/support/vendors/", name="supportVendors")
     */
    public function vendorsAction(Request $request)
    {
        return $this->render('/support/pages/vendors.html.twig');
    }

    /**
     * @Route("/support/general/", name="supportGeneral")
     */
    public function generalAction(Request $request)
    {
        return $this->render('/support/pages/general.html.twig');
    }

    /**
     * @Route("/support/multisig/", name="supportNultisig")
     */
    public function multisigAction(Request $request)
    {
        return $this->render('/support/pages/multisig.html.twig');
    }

    /**
     * @Route("/support/experience/", name="supportExperience")
     */
    public function experienceAction(Request $request)
    {
        return $this->render('/support/pages/experience.html.twig');
    }

    /**
     * @Route("/support/signing/", name="supportSigning")
     */
    public function signingAction(Request $request)
    {
        return $this->render('/support/pages/signing.html.twig');
    }

    /**
     * @Route("/support/privacy/", name="supportPrivacy")
     */
    public function privacyAction(Request $request)
    {
        return $this->render('/support/pages/privacy.html.twig');
    }

    /**
     * @Route("/support/become/", name="supportBecome")
     */
    public function becomeAction(Request $request)
    {
        return $this->render('/support/pages/become.html.twig');
    }

    /**
     * @Route("/support/goods/", name="supportGoods")
     */
    public function goodsAction(Request $request)
    {
        return $this->render('/support/pages/goods.html.twig');
    }

    /**
     * @Route("/support/tor/", name="supportTor")
     */
    public function torAction(Request $request)
    {
        return $this->render('/support/pages/tor.html.twig');
    }

    /**
     * @Route("/contact/", name="contact")
     */
    public function contactAction(Request $request)
    {
        return $this->render('/contact.html.twig');
    }

    /**
     * @Route("/market-pgp/", name="marketPGP")
     */
    public function marketPGPAction(Request $request)
    {
        $key = \file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../config/market_public.key');

        //checks if private key instead of public.
        if (\strpos($key, "PRIVATE") !== false) {
            $key = "The site admin mixed the private key with the public key file.";
        }

        return $this->render('/marketpgp.html.twig', [
            'pgp' => $key,
        ]);
    }
}
