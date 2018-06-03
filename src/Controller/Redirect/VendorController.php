<?php

namespace App\Controller\Redirect;

use App\Form\PGPType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class VendorController extends Controller
{
    /**
     * @Route("/vendor/", name="vendor")
     */
    public function vendorAction(Request $request, $section = '')
    {
        $mesGen = $this->get('App\Service\Mailer');

        $threads = $mesGen->getThreads(10, 0);

        if ($this->getUser()->getRole() == 'vendor') {
            return $this->redirect('/dashboard/');
        }

        if ($this->getUser()->getRole() == 'new_vendor') {
            return $this->redirect('/newvendor/');
        }

        return $this->render('/dashboard/vendor/dashboard.html.twig', [
            'threads' => $threads,
        ]);
    }

    /**
     * @Route("/newvendor/", name="newvendor")
     */
    public function newvendorAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile();

        $session = new Session();

        $pgpForm = $this->createForm(PGPType::class, [], ['pgp' => $profile->getPGP(), 'tfa' => false]);
        $pgpForm->handleRequest($request);

        if ($pgpForm->isSubmitted() && $pgpForm->isValid()) {
            $pgp = $pgpForm->get('pgp')->getData();

            $gpg = new \gnupg();
            $info = $gpg->import($pgp);

            if (is_array($info)) {
                $profile->setPGP($pgp);
                $profile->setFingerprint($info['fingerprint']);
                $em->flush();
                $details = $gpg->keyinfo($info['fingerprint']);
                $session->getFlashBag()->add('pgpSuccess', "Valid PGP key: " . $details[0]['uids'][0]['uid']);
            } else {
                $session->getFlashBag()->add('pgpError', "Invalid PGP key");
            }
            if ($profile->getPgp() != null) {
                $userRepo = $em->getRepository(User::class);
                $user = $userRepo->findOneByUsername($this->getUser()->getUsername());
                $user->setRole('vendor');
                $em->flush();
                return $this->redirect('/logout/');
            } else {
                return $this->redirect($request->getRequestUri());
            }
        }

        return $this->render('/dashboard/vendor/newvendor.html.twig', [
            'pgpForm' => $pgpForm->createView()
        ]);
    }
}
