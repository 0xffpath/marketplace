<?php

namespace App\Controller\Dashboard;

use App\Entity\Listing;
use App\Entity\StaffPick;
use App\Form\Admin\StaffPickType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StaffPickController extends Controller
{

    /**
     * @Route("/staff/pick/", name="staffPick")
     */
    public function pickAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $categoryForm = $this->createForm(StaffPickType::class);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $listingRepo = $em->getRepository(Listing::class);
            $listing = $listingRepo->findOneByUuid($categoryForm->get('listing')->getData());

            if (!empty($listing)) {
                $category = new StaffPick();
                $category->setListing($categoryForm->get('listing')->getData());
                $category->setTitle($listing->getTitle());
                $em->persist($category);
                $em->flush();
                $em->clear();
            } else {
                $session = new Session();
                $session->getFlashBag()->add('error', 'This listing does not exist.');
            }

            return $this->redirectToRoute('staffPick');
        }

        $staffPickRepo = $em->getRepository(StaffPick::class);
        $staffPicks = $staffPickRepo->findAll();

        return $this->render('/dashboard/admin/staffpick.html.twig', [
            'pickForm' => $categoryForm->createView(),
            'staffPicks' => $staffPicks,
        ]);
    }

    /**
     * @Route("/staff/pick/delete/{id}/", name="pickDelete")
     */
    public function pickDeleteAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() == 'admin') {
            $em = $this->getDoctrine()->getManager();

            $staffPickRepo = $em->getRepository(StaffPick::class);
            $staffPick = $staffPickRepo->findOneById($id);
            $em->remove($staffPick);
            $em->flush();

            return $this->redirect('/staff/pick/');
        }
    }
}
