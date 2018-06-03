<?php

namespace App\Controller;

use App\Entity\ReferralBalance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\BuyerProfile;
use App\Entity\VendorProfile;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\RegisterType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AuthController extends Controller
{

    /**
     * @Route("/login/", name="login")
     */
    public function loginAction(Request $request, $twoFactor = '', $cat = '', AuthenticationUtils $authUtils)
    {

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();
        if ($request->query->get('tfa')) {
            $lastUsername = $request->query->get('tfa');
        }

        $cypher = '';
        $twoFactor = '';
        $role = '';
        if ($request->query->get('tfa') && $request->query->get('role')) {
            $role = $request->query->get('role');
            $twoFactor = $request->query->get('tfa');

            $answer = bin2hex(openssl_random_pseudo_bytes(24));
            $session = new Session();
            $session->set('answer', $answer);

            $em = $this->getDoctrine()->getManager();

            $userRepo = $em->getRepository(User::class);
            $user = $userRepo->findOneByUsername($twoFactor);

            if ($user->getRole() == 'buyer') {
                $profileRepo = $em->getRepository(BuyerProfile::class);
            } else {
                $profileRepo = $em->getRepository(VendorProfile::class);
            }

            $user = $profileRepo->findOneByUsername($twoFactor);
            if ($user == null) {
                return $this->redirect('/twofactor/?error=user');
            }
            $fingerprint = $user->getFingerprint();

            if ($fingerprint == null) {
                return $this->redirect('/login/?error=tfa');
            } else {
                $gpg = new \gnupg();
                $gpg -> addencryptkey($fingerprint);
                $cypher = $gpg -> encrypt($answer);
            }
        }

        return $this->render('/auth/login.html.twig', [
            'twoFactor' => $twoFactor,
            'last_username' => $lastUsername,
            'error'         => $error,
            'cypher' => $cypher,
            'role' => $role,
        ]);
    }

    /**
     * @Route("/twofactor/", name="twofactor")
     */
    public function twofactorAction(Request $request)
    {
        $username = $request->request->get('_username');
        $role = $request->request->get('_role');
        $error = $request->query->get('error');

        if (!empty($username) && !empty($role)) {
            return $this->redirect('/login/?tfa=' . $username . '&role=' . $role);
        }

        return $this->render('/auth/twofactor.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register/{token}/")
     * @Route("/register/cat/{cat}/")
     * @Route("/register/", name="register")
     */
    public function registerAction(Request $request, $token = 'MARKET', $cat = '', UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        $securimage = $this->get('App\Service\Securimage');

        $captchaError = false;
        if ($form->isSubmitted() && $form->isValid()) {
            if ($securimage->check($request->request->get('_captcha')) == true) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $user->setPin($form->get('pin')->getData());
                $user->setRole($form->get('role')->getData());
                $user->setUsername($form->get('username')->getData());

                $em = $this->getDoctrine()->getManager();

                $referralBalance = new ReferralBalance();
                $referralBalance->setUsername($form->get('username')->getData());
                $em->persist($referralBalance);
                $em->flush();
                $em->clear();

                $profile = '';
                if ($user->getRole() == 'buyer') {
                    $profile = new BuyerProfile();
                    $profile->setToken($request->request->get('_token'));
                } elseif ($user->getRole() == 'new_vendor') {
                    $profile = new VendorProfile();
                }

                $profile->setUsername($form->get('username')->getData());
                $profile->setjoinToken($request->get('_token'));
                $profile->setJoinDate(time());
                $profile->setToken(bin2hex(openssl_random_pseudo_bytes(20)));

                $em->persist($user);
                $em->persist($profile);
                $em->flush();

                return $this->redirectToRoute('login');
            } else {
                $captchaError = true;
            }
        }

        return $this->render('/auth/register.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
            'captchaError' => $captchaError,
        ]);
    }

    /**
     * @Route("/logout/", name="logout")
     */
    public function logoutAction(Request $request)
    {
    }
}
