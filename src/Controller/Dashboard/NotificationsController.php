<?php

namespace App\Controller\Dashboard;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class NotificationsController extends Controller
{

    /**
     * @Route("/notification/remove/{id}/", name="removeNotification")
     */
    public function notificationRemoveAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $notificationRepo = $em->getRepository(Notification::class);
        $notification = $notificationRepo->findOneBy(['username' => $this->getUser()->getUsername(), 'id' => $id]);
        $em->remove($notification);
        $em->flush();
        $em->clear();

        return $this->redirect('/dashboard/');
    }

    /**
     * @Route("/notification/removeall/", name="removeNotificationAll")
     */
    public function notificationRemoveAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $notificationRepo = $em->getRepository(Notification::class);
        $notifications = $notificationRepo->findByUsername($this->getUser()->getUsername());
        foreach ($notifications as $notification) {
            $notification = $em->merge($notification);
            $em->remove($notification);
            $em->flush();
            $em->clear();
        }

        return $this->redirect('/dashboard/');
    }
}
