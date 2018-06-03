<?php

namespace App\Controller\Dashboard;

use App\Entity\Listing;
use App\Entity\Report;
use App\Form\Admin\ModerateListingsType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ReportController extends Controller
{

    /**
     * @Route("/staff/report/reject/{id}/", name="rejectReport")
     */
    public function reportRejectAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() == 'admin') {
            $em = $this->getDoctrine()->getManager();

            $reportRepo = $em->getRepository(Report::class);
            $report = $reportRepo->findOneById($id);
            $uuid = $report->getListing();
            $em->remove($report);
            $em->flush();
            $em->clear();

            $listingRepo = $em->getRepository(Listing::class);
            $listing = $listingRepo->findOneByUuid($uuid);
            $listing->setFlag(false);
            $em->merge($listing);
            $em->flush();
            $em->clear();

            return $this->redirect('/staff/reports/');
        }
    }

    /**
     * Deletes listing
     *
     * @Route("/staff/report/delete/{id}/", name="deleteReport")
     */
    public function reportDeleteAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() == 'admin') {
            $em = $this->getDoctrine()->getManager();

            $reportRepo = $em->getRepository(Report::class);
            $report = $reportRepo->findOneById($id);
            $uuid = $report->getListing();
            $em->remove($report);
            $em->flush();
            $em->clear();

            $listingRepo = $em->getRepository(Listing::class);
            $listing = $listingRepo->findOneByUuid($uuid);

            $mesGen = $this->get('App\Service\MessageGenerator');
            $thread = $mesGen->setThread($listing->getTitle() . ' has been removed.');

            $mesGen->setThreadUser($this->getUser()->getUsername(), $thread);
            $mesGen->setThreadUser($listing->getUsername(), $thread);

            $mesGen->setMessage($listing->getTitle() . ' has been removed for a rule violation.', $thread);

            $em->remove($listing);
            $em->flush();
            $em->clear();

            return $this->redirect('/staff/reports/');
        }
    }

    /**
     * @Route("/staff/reports/", name="reports")
     */
    public function listingsStaffAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $reportRepo = $em->getRepository(Report::class);
        $report = $reportRepo->findOneBy([], [], ['limit' => 1]);

        $listingRepo = $em->getRepository(Listing::class);

        $listing = '';
        $moderateView = '';

        if ($report != null) {
            $listing = $listingRepo->findOneByUuid($report->getListing());

            $moderate = $this->createForm(ModerateListingsType::class, [], ['listing' => $listing->getId()]);
            $moderate->handleRequest($request);

            if ($moderate->isSubmitted() && $moderate->isValid()) {
                $listing = $listingRepo->findOneById($moderate->get('listing')->getData());
                $listing->setFlag(true);
                $listing->setFlagReason($moderate->get('reason')->getData());
                $em->merge($listing);
                $em->flush();
                $em->clear();

                $reportRepo = $em->getRepository(Report::class);
                $report = $reportRepo->findOneById($report->getId());
                $em->remove($report);
                $em->flush();
                $em->clear();

                $mesGen = $this->get('App\Service\MessageGenerator');
                $thread = $mesGen->setThread($listing->getTitle() . ' has been flagged.');

                $mesGen->setThreadUser($this->getUser()->getUsername(), $thread);
                $mesGen->setThreadUser($listing->getUsername(), $thread);

                $mesGen->setMessage('Your listing [url]http://localhost/listing/edit/' . $moderate->get('listing')->getData() .'/[/url] has been removed for the following reason:
                
                '. $moderate->get('reason')->getData(), $thread);

                return $this->redirectToRoute('reports');
            }

            $moderateView = $moderate->createView();
        }


        return $this->render('/dashboard/admin/reports.html.twig', [
            'listing' => $listing,
            'moderateForm' => $moderateView,
            'report' => $report
        ]);
    }
}
