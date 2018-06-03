<?php

namespace App\Controller;

use App\Entity\Report;
use App\Form\ReportType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ReportController extends Controller
{

    /**
     * @Route("/report/{listing}/", name="report")
     */
    public function reportAction(Request $request, $listing)
    {
        $reportForm = $this->createForm(ReportType::class);
        $reportForm->handleRequest($request);


        $submit = '';
        if ($reportForm->isSubmitted() && $reportForm->isValid()) {
            $securimage = $this->get('App\Service\Securimage');

            if ($securimage->check($request->request->get('_captcha')) == true) {
                $em = $this->getDoctrine()->getManager();
                $report = new Report();
                $report->setUsername($this->getUser()->getUsername());
                $report->setListing($listing);
                $report->setOffense($reportForm->get('offense')->getData());
                $em->persist($report);
                $em->flush();
                $em->clear();
                $submit = true;
            } else {
                $session = new Session();
                $session->getFlashBag()->add('captchaError', "Invalid captcha.");
            }
        }

        return $this->render('/report.html.twig', [
            'listing' => $listing,
            'reportForm' => $reportForm->createView(),
            'submit' => $submit
        ]);
    }
}
