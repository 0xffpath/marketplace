<?php

namespace App\Controller\Dashboard;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DashboardController extends Controller
{

    /**
     * @Route("/dashboard/{section}/", defaults={"section" = ""})
     * @Route("/dashboard/", name="dashboard")
     */
    public function dashboardAction(Request $request, $section = '')
    {
        $mesGen = $this->get('App\Service\Mailer');

        $threads = $mesGen->getUnreadThreads();

        $em = $this->getDoctrine()->getManager();
        $notificationRepo = $em->getRepository(Notification::class);
        $notifications = $notificationRepo->findByUsername($this->getUser()->getUsername(), ['id' => 'desc']);

        return $this->render('/dashboard/buyer/dashboard.html.twig', [
            'threads' => $threads,
            'notifications' => $notifications,
            'profile' => $this->get('App\Service\Profile')->getProfile(),
        ]);
    }

    /**
     * @Route("/staff/dashboard/{section}/", defaults={"section" = ""})
     * @Route("/staff/dashboard/", name="staffDashboard")
     */
    public function dashboardStaffAction(Request $request, $section = '')
    {
        $mesGen = $this->get('App\Service\Mailer');

        $threads = $mesGen->getUnreadThreads();

        return $this->render('/dashboard/admin/dashboard.html.twig', [
            'threads' => $threads,
        ]);
    }
}
