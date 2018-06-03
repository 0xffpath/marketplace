<?php

namespace App\Controller\Purchase;

use App\Entity\Listing;
use App\Entity\Orders;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PayController extends Controller
{

    /**
     * @Route("/pay/{order}/", name="pay")
     */
    public function payAction(Request $request, $order)
    {
        $em = $this->getDoctrine()->getManager();

        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneByUuid($order);


        $gpg = new \gnupg();
        $info = $gpg->import(\file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/../config/market_private.key'));
        $gpg->addsignkey($info['fingerprint']);
        $sign = $gpg->sign(
            "This is a valid address." . $order->getAddress()
        );

        //calculate time to put in clock
        $timeMinutes = floor((($order->getStartDate()+900)-time())/60);
        $timeSeconds = sprintf('%02d', floor((((($order->getStartDate()+900)-time())/60)-$timeMinutes)*60));

        //if time out, then lock the order
        $timeout = false;
        if ($timeMinutes < 0) {

            //check if already changed
            if($order->getStatus() == 'waiting'){
                $listingRepo = $em->getRepository(Listing::class);
                $listing = $listingRepo->findOneByUuid($order->getListing());
                $listing->setStock($listing->getStock() + 1);
                $em->persist($listing);
                $em->flush();
                $em->clear();
            }

            $timeout = true;
            $order->setStatus('timeout');
            $order->setBootstrap('warning');
            $em->persist($order);
            $em->flush();
            $em->clear();
        }

        $wallet = $this->get('App\Service\Wallet\WalletFactory');

        $address = null;
        if($order->getCrypto() == 'btc') {
            $bitcoin = $wallet->create('bitcoin');
            if($bitcoin->getaddressbalance($order->getAddress())['unconfirmed']  >= $order->getCryptoTotal() && !$order->getRecieved()){
                $order->setRecieved(true);
                $em->persist($order);
                $em->flush();
                $em->clear();
                return $this->redirect('/pay/' . $order->getUuid() . '/');
            }
        }

        //percentage to put in bar width
        $timeLeft =  floor((1-(((($order->getStartDate()+900)-time())))/900)*100);

        //how long bar should go on for
        $secondsLeft = sprintf('%02d', floor((((($order->getStartDate()+900)-time())))));

        return $this->render('/pay.html.twig', [
            'item' => [
                'order' => $order,
            ],
            'timeSeconds' => $timeSeconds,
            'secondsLeft' => $secondsLeft,
            'paid' => $order->getRecieved(),
            'timeMinutes' => $timeMinutes,
            'timeLeft' => $timeLeft,
            'time' => $order->getStartDate()+600,
            'timeout' => $timeout,
            'sign' => $sign,
        ]);
    }
}
