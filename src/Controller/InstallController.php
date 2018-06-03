<?php

namespace App\Controller;

use App\Entity\AdminProfile;
use App\Entity\User;
use App\Form\Admin\InstallType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InstallController extends Controller
{

    /**
     * @Route("/install/", name="install")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();

        $userRepo = $em->getRepository(User::class);
        $users = $userRepo->findByRole('admin');

        $installed = file_get_contents(__DIR__ . '/../../.installed');

        if (empty($installed) && count($users) == 0) {
            $user = new User();
            $form = $this->createForm(InstallType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $password = $passwordEncoder->encodePassword($user, $form->get('password')->getData());
                $user->setPassword($password);
                $user->setPin($form->get('pin')->getData());
                $user->setRole('admin');
                $user->setUsername($form->get('username')->getData());

                $pgp = $form->get('pgp')->getData();
                $gpg = new \gnupg();
                $info = $gpg->import($pgp);

                $twoFactor = 0;
                if ($form->get('tfa')->getData() == 'on') {
                    $twoFactor = 1;
                }

                $profile = new AdminProfile();
                $profile->setUsername('admin');

                if (is_array($info)) {
                    $profile->setTwoFactor($twoFactor);
                    $profile->setPGP($pgp);
                    $profile->setFingerprint($info['fingerprint']);
                    $em->flush();
                } else {
                    $form->addError(new FormError('Invalid PGP key'));
                }

                $em->persist($user);
                $em->persist($profile);
                $em->flush();
                file_put_contents(__DIR__ . '/../../.installed', 'installed');
                return $this->redirectToRoute('login');
            }

            return $this->render('/install.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('home');
    }
}
