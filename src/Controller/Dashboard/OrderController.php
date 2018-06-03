<?php

namespace App\Controller\Dashboard;

use App\Entity\Feedback;
use App\Entity\Listing;
use App\Entity\Notification;
use App\Entity\Orders;
use App\Entity\VendorProfile;
use App\Form\ReviewType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Form\MessageType;

class OrderController extends Controller
{

    /**
     * @Route("/orders/", name="orders")
     */
    public function ordersAction(Request $request, $section = '')
    {
        $em = $this->getDoctrine()->getManager();

        $ordersRepo = $em->getRepository(Orders::class);

        $page = 1;
        if (is_numeric($request->query->get('page'))) {
            $page = $request->query->get('page');
        }

        $orders = [];
        if ($this->getUser()->getRole() == 'vendor') {
            $orders = $ordersRepo->findByVendor($this->getUser()->getUsername(), ['id' => 'DESC'], 10, ($page*10)-10);
        }
        if ($this->getUser()->getRole() == 'buyer') {
            $orders = $ordersRepo->findByBuyer($this->getUser()->getUsername(), ['id' => 'DESC'], 10, ($page*10)-10);
        }

        $totalpages = ceil($this->get('App\Service\DashboardNotifications')->getTotalOrders()/10);

        return $this->render('/dashboard/buyer/orders.html.twig', [
            'orders' => $orders,
            'totalPages' => $totalpages,
            'page' => $page,
        ]);
    }

    /**
     * @Route("/order/{uuid}/", name="order")
     */
    public function orderAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $messageForm = $this->createForm(MessageType::class);
        $messageForm->handleRequest($request);

        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneByUuid($uuid);

        $mesGen = $this->get('App\Service\Mailer');

        $messages = $mesGen->getMessages($uuid);
        $thread = $mesGen->getThread($uuid);

        $feedbackRepo = $em->getRepository(Feedback::class);
        $userFeedback = $feedbackRepo->findOneByfOrder($uuid);

        $reviewForm = $this->createForm(ReviewType::class);
        $reviewForm->handleRequest($request);

        if ($request->query->get('edit') == 'true' && $order->getReviewed()) {
            $reviewForm = $this->createForm(ReviewType::class, [], [
                'feedback' => $userFeedback->getFeedback(),
                'comment' => $userFeedback->getComment()
            ]);
            $reviewForm->handleRequest($request);
        }

        $profile = $this->get('App\Service\Profile')->getProfile();

        $vendorRepo = $em->getRepository(VendorProfile::class);
        $vendor = $vendorRepo->findOneByUsername($order->getVendor());

        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            $mesGen->setMessage($messageForm->get('message')->getData(), $uuid);
            $mesGen->setThreadStatus($uuid, false, false);
            return $this->redirect($request->getRequestUri());
        }

        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            if (($order->getBuyer() == $this->getUser()->getUsername()) && $order->getStatus() == 'finalized') {
                if ($order->getReviewed() == true) {
                    switch ($userFeedback->getFeedback()) {
                        case 'Positive':
                            $vendor->setPositive($vendor->getPositive()-1);
                            $vendor->setTotalFeedback($vendor->getTotalFeedback()-1);
                            $em->merge($vendor);
                            $em->flush();
                            $em->clear();
                            break;
                        case 'Neutral':
                            $vendor->setNeutral($vendor->getNeutral()-1);
                            $vendor->setTotalFeedback($vendor->getTotalFeedback()-1);
                            $em->merge($vendor);
                            $em->flush();
                            $em->clear();
                            break;
                        case 'Negative':
                            $vendor->setNegative($vendor->getNegative()-1);
                            $vendor->setTotalFeedback($vendor->getTotalFeedback()-1);
                            $em->merge($vendor);
                            $em->flush();
                            $em->clear();
                            break;
                    }
                    $userFeedback = $em->merge($userFeedback);
                    $em->remove($userFeedback);
                    $em->flush();
                    $em->clear();
                }

                switch ($reviewForm->get('feedback')->getData()) {
                    case 'Positive':
                        $vendor->setPositive($vendor->getPositive()+1);
                        $vendor->setTotalFeedback($vendor->getTotalFeedback()+1);
                        $em->merge($vendor);
                        $em->flush();
                        $em->clear();
                        break;
                    case 'Neutral':
                        $vendor->setNeutral($vendor->getNeutral()+1);
                        $vendor->setTotalFeedback($vendor->getTotalFeedback()+1);
                        $em->merge($vendor);
                        $em->flush();
                        $em->clear();
                        break;
                    case 'Negative':
                        $vendor->setNegative($vendor->getNegative()+1);
                        $vendor->setTotalFeedback($vendor->getTotalFeedback()+1);
                        $em->merge($vendor);
                        $em->flush();
                        $em->clear();
                        break;
                }

                $feedback = new Feedback();
                $feedback->setComment($reviewForm->get('comment')->getData());
                $feedback->setFeedback($reviewForm->get('feedback')->getData());
                $feedback->setfOrder($order->getUuid());
                $feedback->setVendor($order->getVendor());
                $feedback->setBuyer($order->getBuyer());
                $feedback->setListing($order->getListing());
                $em->persist($feedback);
                $em->flush();
                $em->clear();

                $order->setReviewed(true);
                $em->merge($order);
                $em->flush();
                $em->clear();

                $reviews = $feedbackRepo->findByListing($order->getListing());

                $total = 0;
                foreach ($reviews as $review) {
                    switch ($review->getFeedback()) {
                        case 'Positive':
                            $total++;
                            break;
                        case 'Neutral':
                            $total += 0.6;
                            break;
                    }
                }

                $rating = $total/count($reviews);

                $listingRepo = $em->getRepository(Listing::class);
                $listing = $listingRepo->findOneByUuid($order->getListing());
                $listing->setRating($rating*100);

                $em->merge($listing);
                $em->flush();
                $em->clear();

                return $this->redirect('/order/' . $uuid . '/');
            }
        }

        $time = $order->getAutoDate() - time();

        $timeLeft = [
            'days' => floor($time/86400),
            'minutes' => round(($time - (floor($time/86400) * 86400))/1440, 0),
        ];

        return $this->render('/dashboard/buyer/order.html.twig', [
            'order' => $order,
            'feedback' => $userFeedback,
            'messages' => $messages,
            'messageForm' => $messageForm->createView(),
            'reviewForm' => $reviewForm->createView(),
            'edit' => $request->query->get('edit'),
            'vendor' => $vendor,
            'thread' => $thread,
            'timeLeft' => $timeLeft,
        ]);
    }

    /**
     * @Route("/order/accept/{id}/", name="acceptOrder")
     */
    public function orderAcceptAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneBy(['vendor' => $this->getUser()->getUsername(), 'uuid' => $id]);
        $order->setStatus('accepted');
        $order->setBootstrap('success');
        $em->merge($order);
        $em->flush();
        $em->clear();

        $notification = new Notification();
        $notification->setType('accepted');
        $notification->setUsername($order->getBuyer());
        $notification->setAmount($order->getAmount());
        $notification->setBootstrap('success');
        $notification->setTitle($order->getTitle());
        $em->persist($notification);
        $em->flush();
        $em->clear();

        return $this->redirect('/order/' . $id . '/');
    }

    /**
     * @Route("/order/reject/{id}/", name="rejectOrder")
     */
    public function orderRejectAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneBy(['vendor' => $this->getUser()->getUsername(), 'uuid' => $id]);
        $order->setStatus('rejected');
        $order->setBootstrap('danger');
        $em->merge($order);
        $em->flush();
        $em->clear();

        $notification = new Notification();
        $notification->setType('rejected');
        $notification->setUsername($order->getBuyer());
        $notification->setAmount($order->getAmount());
        $notification->setBootstrap('danger');
        $notification->setTitle($order->getTitle());
        $em->persist($notification);
        $em->flush();
        $em->clear();
        return $this->redirect('/order/' . $id . '/');
    }

    /**
     * @Route("/order/cancel/{id}/", name="cancelOrder")
     */
    public function orderCancelAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneBy(['vendor' => $this->getUser()->getUsername(), 'uuid' => $id]);
        $order->setStatus('canceled');
        $order->setBootstrap('danger');
        $em->merge($order);
        $em->flush();
        $em->clear();

        $notification = new Notification();
        $notification->setType('canceled');
        $notification->setUsername($order->getBuyer());
        $notification->setAmount($order->getAmount());
        $notification->setBootstrap('danger');
        $notification->setTitle($order->getTitle());
        $em->persist($notification);
        $em->flush();
        $em->clear();
        return $this->redirect('/order/' . $id . '/');
    }

    /**
     * @Route("/order/dispute/{id}/", name="disputeOrder")
     */
    public function orderDisputeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneBy(['buyer' => $this->getUser()->getUsername(), 'uuid' => $id]);
        $order->setStatus('disputed');
        $order->setBootstrap('danger');
        $em->merge($order);
        $em->flush();
        $em->clear();

        $notification = new Notification();
        $notification->setType('disputed');
        $notification->setUsername($order->getBuyer());
        $notification->setAmount($order->getAmount());
        $notification->setBootstrap('danger');
        $notification->setTitle($order->getTitle());
        $em->persist($notification);
        $em->flush();
        $em->clear();
        return $this->redirect('/order/' . $id . '/');
    }

    /**
     * @Route("/order/ship/{id}/", name="shipOrder")
     */
    public function orderShipAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneBy(['vendor' => $this->getUser()->getUsername(), 'uuid' => $id]);
        $order->setStatus('shipped');
        $order->setShippedDate(time());
        $order->setAutoDate(time() + 259200);
        $order->setBootstrap('success');
        $em->merge($order);
        $em->flush();
        $em->clear();

        $notification = new Notification();
        $notification->setType('shipped');
        $notification->setUsername($order->getBuyer());
        $notification->setAmount($order->getAmount());
        $notification->setBootstrap('success');
        $notification->setTitle($order->getTitle());
        $em->persist($notification);
        $em->flush();
        $em->clear();
        return $this->redirect('/order/' . $id . '/');
    }

    /**
     * @Route("/order/finalize/{id}/", name="finalizeOrder")
     */
    public function orderFinalizeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneBy(['buyer' => $this->getUser()->getUsername(), 'uuid' => $id]);
        $order->setStatus('finalized');
        $order->setBootstrap('success');
        $order->setAutoDate(time());
        $em->merge($order);
        $em->flush();
        $em->clear();

        $vendorRepo = $em->getRepository(VendorProfile::class);
        $vendor = $vendorRepo->findOneByUsername($order->getVendor());
        $vendor->setTotalSell($vendor->getTotalSell()+1);
        $em->merge($order);
        $em->flush();
        $em->clear();

        $this->get('App\Service\Experience')->update($order->getVendor(), round($order->getPrice()*10));
        $this->get('App\Service\Experience')->update($this->getUser()->getUsername(), round($order->getPrice()*10));

        $notification = new Notification();
        $notification->setType('finalized');
        $notification->setUsername($order->getVendor());
        $notification->setAmount($order->getAmount());
        $notification->setBootstrap('success');
        $notification->setTitle($order->getTitle());
        $em->persist($notification);
        $em->flush();
        $em->clear();
        return $this->redirect('/order/' . $id . '/');
    }

    /**
     * @Route("/order/extend/{id}/", name="extendOrder")
     */
    public function orderExtendAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $orderRepo = $em->getRepository(Orders::class);

        $order = [];
        if ($this->getUser()->getRole() == 'vendor') {
            $order = $orderRepo->findOneBy(['vendor' => $this->getUser()->getUsername(), 'uuid' => $id]);
        }
        if ($this->getUser()->getRole() == 'buyer') {
            $order = $orderRepo->findOneBy(['buyer' => $this->getUser()->getUsername(), 'uuid' => $id]);
        }

        $order->setAutoDate(time()+ 259200);
        $em->merge($order);
        $em->flush();
        $em->clear();

        return $this->redirect('/order/' . $id . '/');
    }
}
