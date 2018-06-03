<?php

namespace App\Controller\Dashboard;

use App\Entity\ReferralBalance;
use App\Form\ReferralWithdrawType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AffiliatesController extends Controller
{

    /**
     * @Route("/affiliates/", name="affiliates")
     */
    public function affiliatesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile();

        $token = $profile->getToken();

        $referralBalance = $em->getRepository(ReferralBalance::class);
        $referralBalance = $referralBalance->findOneByUsername($this->getUser()->getUsername());

        $zcash = $referralBalance->getZcashBalance();
        $monero = $referralBalance->getMoneroBalance();

        $withdrawForm = $this->createForm(ReferralWithdrawType::class);
        $withdrawForm->handleRequest($request);

        if ($withdrawForm->isSubmitted() && $withdrawForm->isValid()) {
        }

        $http = 'http';
        if (isset($_SERVER['HTTPS'])) {
            $http = 'https';
        }

        $link = $http . '://' . $_SERVER['HTTP_HOST'] . "/register/" . $token;
        return $this->render('/dashboard/shared/affiliates.html.twig', [
            'link' => $link,
            'monero' => $monero,
            'zcash' => $zcash,
            'withdrawForm' => $withdrawForm->createView(),
        ]);
    }
}
