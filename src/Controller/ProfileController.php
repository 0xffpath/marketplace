<?php

namespace App\Controller;

use App\Entity\Feedback;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProfileController extends Controller
{

    /**
     * @Route("/profile/{username}/", name="profile")
     */
    public function profileAction(Request $request, $username)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile($username);
        $role = $this->get('App\Service\Profile')->getRole($username);

        if ($profile == null) {
            throw $this->createNotFoundException('This user was not found.');
        }

        $feedback = "";
        if ($role == "vendor") {
            $feedbackRepo = $em->getRepository(Feedback::class);
            $feedback = $feedbackRepo->createQueryBuilder('r')
                ->select('r')
                ->where('r.vendor = :vendor')
                ->orderBy('r.id', 'DESC')
                ->setParameters(['vendor' => $username])
                ->getQuery()
                ->getArrayResult();
        }

        return $this->render('/profile.html.twig', [
            'user' => $profile,
            'feedback' => $feedback,
            'pgp' => $profile->getPGP(),
            'role' => $role,
        ]);
    }
}
