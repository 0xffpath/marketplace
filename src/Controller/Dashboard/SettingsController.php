<?php

namespace App\Controller\Dashboard;

use App\Entity\AdminProfile;
use App\Entity\BuyerProfile;
use App\Entity\Listing;
use App\Entity\ShippingOption;
use App\Entity\VendorProfile;
use App\Form\AccountType;
use App\Form\Buyer\CryptoAddressType;
use App\Form\ProfileType;
use App\Form\Vendor\CryptoVendorAddressType;
use App\Form\CurrencyType;
use App\Form\PGPType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class SettingsController extends Controller
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/pgp/", name="pgp")
     * @Route("/staff/pgp/", name="staffPGP")
     */
    public function pgpAction(Request $request, $section = '')
    {
        $em = $this->getDoctrine()->getManager();

        $profile = null;
        if ($this->getUser()->getRole() == 'buyer') {
            $profileRepo = $em->getRepository(BuyerProfile::class);
            $profile = $profileRepo->findOneByUsername($this->getUser()->getUsername());
        }

        if ($this->getUser()->getRole() == 'vendor') {
            $profileRepo = $em->getRepository(VendorProfile::class);
            $profile = $profileRepo->findOneByUsername($this->getUser()->getUsername());
        }

        if ($this->getUser()->getRole() == 'admin') {
            $profileRepo = $em->getRepository(AdminProfile::class);
            $profile = $profileRepo->findOneByUsername($this->getUser()->getUsername());
        }

        $twoFactor = false;
        if ($profile->getTwoFactor() == 1) {
            $twoFactor = true;
        }

        $pgpForm = $this->createForm(PGPType::class, [], ['pgp' => $profile->getPgp(), 'tfa' => $twoFactor]);
        $pgpForm->handleRequest($request);

        $session = new Session();

        if ($pgpForm->isSubmitted() && $pgpForm->isValid()) {
            $pgp = $pgpForm->get('pgp')->getData();
            $gpg = new \gnupg();
            $info = $gpg->import($pgp);

            $twoFactor = 0;
            if ($pgpForm->get('tfa')->getData() == 'on') {
                $twoFactor = 1;
            }

            if (is_array($info)) {
                $profile->setTwoFactor($twoFactor);
                $profile->setPGP($pgp);
                $profile->setFingerprint($info['fingerprint']);
                $em->flush();
                $details = $gpg->keyinfo($info['fingerprint']);
                $session->getFlashBag()->add('pgpSuccess', "Valid PGP key: " . $details[0]['uids'][0]['uid']);
            } else {
                $session->getFlashBag()->add('pgpError', "Invalid PGP key.");
            }

            return $this->redirect($request->getRequestUri());
        }

        return $this->render('/dashboard/shared/pgp.html.twig', [
            'pgpForm' => $pgpForm->createView(),
            'user' => $profile,
        ]);
    }

    /**
     * @Route("/currency/", name="currency")
     */
    public function currencyAction(Request $request, $section = '')
    {
        if ($this->getUser()->getRole() == 'vendor') {
            return $this->redirect('/currency/vendor/');
        }

        $em = $this->getDoctrine()->getManager();

        $profileRepo = $em->getRepository(BuyerProfile::class);
        $profile = $profileRepo->findOneByUsername($this->getUser()->getUsername());

        $currencyForm = $this->createForm(CurrencyType::class, [], ['currency' => $profile->getFiat()]);
        $currencyForm->handleRequest($request);

        $cryptoForm = $this->createForm(CryptoAddressType::class, [], [
            'bitcoin' => $profile->getBTCAddress(),
            'bitcoinPublic' => $profile->getBTCPublic(),
            'monero' => $profile->getXMRAddress(),
            'zcash' => $profile->getZECAddress(),
        ]);
        $cryptoForm->handleRequest($request);

        $session = new Session();

        if ($currencyForm->isSubmitted() && $currencyForm->isValid()) {
            $currency = $currencyForm->get('currency')->getData();
            $profile->setFiat($currency);
            $em->flush();

            $session->getFlashBag()->add('currencySuccess', "Updated currency.");

            return $this->redirect($request->getRequestUri());
        }

        if ($cryptoForm->isSubmitted() && $cryptoForm->isValid()) {
            if ($cryptoForm->get('pin')->getData() != $this->getUser()->getPin()) {
                $session->getFlashBag()->add('cryptoError', "Invalid Pin.");
            } else {
                $bitcoin = $cryptoForm->get('bitcoin')->getData();
                $bitcoinPublic = $cryptoForm->get('bitcoinPublic')->getData();
                $monero = $cryptoForm->get('monero')->getData();
                $zcash = $cryptoForm->get('zcash')->getData();

                $verify = $this->get('App\Service\CryptoAddressValidator');

                if ($bitcoin != "" && $verify->validateBTCAddress($bitcoin)) {
                    $profile->setBTCAddress($bitcoin);
                    $session->getFlashBag()->add('cryptoSuccess', "Valid Bitcoin address.");
                } elseif ($bitcoin != "" && !$verify->validateBTCAddress($bitcoin)) {
                    $session->getFlashBag()->add('cryptoError', "Invalid Bitcoin address.");
                }

                if ($bitcoinPublic != "" && $verify->validateBTCPublic($bitcoinPublic, $bitcoin)) {
                    $profile->setBTCPublic($bitcoinPublic);
                    $session->getFlashBag()->add('cryptoSuccess', "Valid Bitcoin key.");
                } elseif ($bitcoinPublic !== "" && !$verify->validateBTCPublic($bitcoinPublic, $bitcoin)) {
                    $session->getFlashBag()->add('cryptoError', "Invalid Bitcoin address or key.");
                }

                if ($monero != "" && $verify->validateXMRAddress($monero)) {
                    $profile->setXMRAddress($monero);
                    $session->getFlashBag()->add('cryptoSuccess', "Valid Monero address.");
                } elseif ($monero != "" && !$verify->validateXMRAddress($monero)) {
                    $session->getFlashBag()->add('cryptoError', "Invalid Monero address.");
                }

                if ($zcash != "" && $verify->validateXMRAddress($zcash)) {
                    $profile->setZECAddress($zcash);
                    $session->getFlashBag()->add('cryptoSuccess', "Valid Zcash address.");
                } elseif ($zcash != "" && !$verify->validateZECAddress($zcash)) {
                    $session->getFlashBag()->add('cryptoError', "Invalid Zcash address.");
                }

                $em->flush();
            }
            return $this->redirect($request->getRequestUri());
        }

        return $this->render('/dashboard/buyer/currency.html.twig', [
            'currencyForm' => $currencyForm->createView(),
            'cryptoForm' => $cryptoForm->createView(),
            'user' => $profile
        ]);
    }

    /**
     * @Route("/currency/vendor/", name="currencyVendor")
     */
    public function currencyVendorAction(Request $request, $section = '')
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile();

        $currencyForm = $this->createForm(CurrencyType::class, [], ['currency' => $profile->getFiat()]);
        $currencyForm->handleRequest($request);

        $cryptoForm = $this->createForm(CryptoVendorAddressType::class, [], [
            'bitcoin' => $profile->getBTCPublic(),
            'monero' => $profile->getXMRAddress(),
            'zcash' => $profile->getZECAddress(),
        ]);
        $cryptoForm->handleRequest($request);

        $session = new Session();

        if ($currencyForm->isSubmitted() && $currencyForm->isValid()) {
            $currency = $currencyForm->get('currency')->getData();
            $profile->setFiat($currency);
            $em->flush();

            $listingRepo = $em->getRepository(Listing::class);
            $listings = $listingRepo->findByUsername($this->getUser()->getUsername());

            $shippingRepo = $em->getRepository(ShippingOption::class);
            $options = $shippingRepo->findByUsername($this->getUser()->getUsername());

            foreach ($listings as $listing) {
                $listing->setFiat($currency);
                $em->flush();
            }

            foreach ($options as $option) {
                $option->setFiat($currency);
                $em->flush();
            }

            $session->getFlashBag()->add('currencySuccess', "Updated currency.");

            return $this->redirect($request->getRequestUri());
        }

        if ($cryptoForm->isSubmitted() && $cryptoForm->isValid()) {
            if ($cryptoForm->get('pin')->getData() != $this->getUser()->getPin()) {
                $session->getFlashBag()->add('cryptoError', "Invalid Pin.");
            } else {
                $bitcoin = $cryptoForm->get('bitcoin')->getData();
                $monero = $cryptoForm->get('monero')->getData();
                $zcash = $cryptoForm->get('zcash')->getData();

                $verify = $this->get('App\Service\CryptoAddressValidator');

                if ($bitcoin != "" && $verify->validateBTCMaster($bitcoin)) {
                    $profile->setBTCPublic($bitcoin);
                    $session->getFlashBag()->add('cryptoSuccess', "Valid Bitcoin key.");
                } elseif ($bitcoin != "" && !$verify->validateBTCMaster($bitcoin)) {
                    $session->getFlashBag()->add('cryptoError', "Invalid Bitcoin key.");
                }

                if ($monero != "" && $verify->validateXMRAddress($monero)) {
                    $profile->setXMRAddress($monero);
                    $session->getFlashBag()->add('cryptoSuccess', "Valid Monero address.");
                } elseif ($monero != "" && !$verify->validateXMRAddress($monero)) {
                    $session->getFlashBag()->add('cryptoError', "Invalid Monero address.");
                }

                if ($zcash != "" && $verify->validateXMRAddress($zcash)) {
                    $profile->setZECAddress($zcash);
                    $session->getFlashBag()->add('cryptoSuccess', "Valid Zcash address.");
                } elseif ($zcash != "" && !$verify->validateZECAddress($zcash)) {
                    $session->getFlashBag()->add('cryptoError', "Invalid Zcash address.");
                }

                $em->flush();
            }
            return $this->redirect($request->getRequestUri());
        }

        return $this->render('/dashboard/vendor/currency.html.twig', [
            'currencyForm' => $currencyForm->createView(),
            'cryptoForm' => $cryptoForm->createView(),
            'user' => $profile
        ]);
    }

    /**
     * @Route("/account/", name="account")
     * @Route("/staff/account/", name="staffAccount")
     */
    public function accountAction(Request $request, $section = '')
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $this->get('App\Service\Profile')->getProfile();

        $date = $profile->getJoinDate();
        $type = $this->getUser()->getRole();

        $accountForm = $this->createForm(AccountType::class);
        $accountForm->handleRequest($request);

        $session = new Session();

        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $new = $accountForm->get('handle')->getData();
            $confirm = $accountForm->get('confirm')->getData();
            $old = $accountForm->get('old')->getData();
            $pin = $accountForm->get('pin')->getData();

            switch (true) {
                case $new != $confirm:
                    $session->getFlashBag()->add('accountError', "New password and confirm password do not match.");
                    break;
                case !password_verify($old, $this->getUser()->getPassword()):
                    $session->getFlashBag()->add('accountError', "Incorrect old password.");
                    break;
                case $this->getUser()->getPin() != $pin:
                    $session->getFlashBag()->add('accountError', "Incorrect pin.");
                    break;
                default:
                    $this->getUser()->setPassword($this->encoder->encodePassword($this->getUser(), $new));
                    $em->flush();
                    $session->getFlashBag()->add('accountSuccess', "Updated password.");
                    break;
            }

            return $this->redirect($request->getRequestUri());
        }

        return $this->render('/dashboard/shared/account.html.twig', [
            'accountForm' => $accountForm->createView(),
            'date' => $date,
            'type' => $type,
        ]);
    }

    /**
     * @Route("/profile/", name="profileEdit")
     */
    public function profileEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = new Session();
        $profile = $this->get('App\Service\Profile')->getProfile();

        $profileForm = $this->createForm(ProfileType::class, [], ['profile' => $profile->getProfile()]);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $profile->setProfile($profileForm->get('profile')->getData());
            $em->merge($profile);
            $em->flush();
            $em->clear();
            $session->getFlashBag()->add('profileSuccess', "Profile updated.");
            return $this->redirect('/profile/');
        }

        return $this->render('/dashboard/shared/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
        ]);
    }
}
