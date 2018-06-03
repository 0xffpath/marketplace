<?php

namespace App\Controller\Dashboard;

use App\Entity\Notification;
use App\Entity\Orders;
use App\Entity\VendorProfile;
use App\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DisputeController extends Controller
{

    /**
     * @Route("/staff/disputes/", name="staffDisputes")
     */
    public function disputesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $status = ['vendor', 'buyer', 'disputed',];

        $orderRepo = $em->getRepository(Orders::class);

        $orders = $orderRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.status in(:status)")
            ->setParameter('status', $status, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->orderBy('r.id', 'desc')
            ->getQuery()
            ->getArrayResult();

        return $this->render('/dashboard/admin/disputes.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/staff/dispute/{uuid}/", name="staffDispute")
     */
    public function disputeAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $messageForm = $this->createForm(MessageType::class);
        $messageForm->handleRequest($request);

        $orderRepo = $em->getRepository(Orders::class);
        $order = $orderRepo->findOneByUuid($uuid);

        $mesGen = $this->get('App\Service\Mailer');

        $messages = $mesGen->getMessages($uuid);
        $thread = $mesGen->getThread($uuid);

        $profile = $this->get('App\Service\Profile')->getProfile();

        $vendorRepo = $em->getRepository(VendorProfile::class);
        $vendor = $vendorRepo->findOneByUsername($order->getVendor());

        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            $mesGen->setMessage($messageForm->get('message')->getData(), $uuid);
            $mesGen->setThreadStatus($uuid, false, false);
            return $this->redirect($request->getRequestUri());
        }

        $time = $order->getAutoDate() - time();

        $timeLeft = [
            'days' => floor($time/86400),
            'minutes' => round(($time - (floor($time/86400) * 86400))/1440, 0),
        ];

        return $this->render('/dashboard/buyer/order.html.twig', [
            'order' => $order,
            'messages' => $messages,
            'messageForm' => $messageForm->createView(),
            'edit' => $request->query->get('edit'),
            'vendor' => $vendor,
            'thread' => $thread,
            'timeLeft' => $timeLeft,
        ]);
    }

    /**
     * @Route("/staff/dispute/vendor/{id}/", name="staffDisputeVendor")
     */
    public function disputeVendorAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() == 'admin') {
            $em = $this->getDoctrine()->getManager();
            $orderRepo = $em->getRepository(Orders::class);
            $order = $orderRepo->findOneBy(['uuid' => $id]);
            $order->setStatus('vendor');
            $order->setBootstrap('warning');
            $em->merge($order);
            $em->flush();
            $em->clear();

            $notification = new Notification();
            $notification->setType('refunded');
            $notification->setUsername($order->getBuyer());
            $notification->setAmount($order->getAmount());
            $notification->setBootstrap('danger');
            $notification->setTitle($order->getTitle());
            $em->persist($notification);
            $em->flush();
            $em->clear();

            $notification = new Notification();
            $notification->setType('refunded');
            $notification->setUsername($order->getVendor());
            $notification->setAmount($order->getAmount());
            $notification->setBootstrap('success');
            $notification->setTitle($order->getTitle());
            $em->persist($notification);
            $em->flush();
            $em->clear();

            return $this->redirect('/staff/dispute/' . $id . '/');
        }
    }

    /**
     * @Route("/staff/dispute/buyer/{id}/", name="staffDisputeBuyer")
     */
    public function disputeBuyerAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() == 'admin') {
            $em = $this->getDoctrine()->getManager();
            $orderRepo = $em->getRepository(Orders::class);
            $order = $orderRepo->findOneBy(['uuid' => $id]);
            $order->setStatus('buyer');
            $order->setBootstrap('warning');
            $em->merge($order);
            $em->flush();
            $em->clear();

            $notification = new Notification();
            $notification->setType('refunded');
            $notification->setUsername($order->getBuyer());
            $notification->setAmount($order->getAmount());
            $notification->setBootstrap('green');
            $notification->setTitle($order->getTitle());
            $em->persist($notification);
            $em->flush();
            $em->clear();

            $notification = new Notification();
            $notification->setType('refunded');
            $notification->setUsername($order->getVendor());
            $notification->setAmount($order->getAmount());
            $notification->setBootstrap('danger');
            $notification->setTitle($order->getTitle());
            $em->persist($notification);
            $em->flush();
            $em->clear();

            return $this->redirect('/staff/dispute/' . $id . '/');
        }
    }
}
