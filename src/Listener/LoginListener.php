<?php
namespace App\Listener;

use App\Entity\AdminProfile;
use App\Entity\BuyerProfile;
use App\Entity\VendorProfile;
use App\Exception\CaptchaException;
use App\Service\Securimage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManager;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginListener
{
    protected $em;
    protected $securimage;

    public function __construct(EntityManager $em, Securimage $securimage)
    {
        $this->em = $em;
        $this->securimage = $securimage;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $request = $event->getRequest();

        $user = $event->getAuthenticationToken()->getUser();

        if ($this->securimage->check($request->request->get('_captcha')) == false) {
            throw new CaptchaException();
        }

        if ($user->getRole() == 'buyer') {
            $profileRepo = $this->em->getRepository(BuyerProfile::class);
        } elseif ($user->getRole() == 'vendor' || $user->getRole() == 'new_vendor') {
            $profileRepo = $this->em->getRepository(VendorProfile::class);
        } elseif ($user->getRole() == 'admin') {
            $profileRepo = $this->em->getRepository(AdminProfile::class);
        }

        $userProfile = $profileRepo->findOneByUsername($request->request->get('_username'));

        $tfa = $request->request->get('_tfa');

        if ($userProfile->getTwoFactor()) {
            if (empty($tfa)) {
                throw new BadCredentialsException();
            }
        }

        $session = new Session();

        if ($userProfile->getTwoFactor() && !empty($tfa)) {
            if ($tfa !== $session->get('answer')) {
                throw new BadCredentialsException();
            }
        }

        $session->remove('answer');

        if ($user instanceof User && $user->getRole() === 'vendor') {
            $vendorRepo = $this->em->getRepository(VendorProfile::class);
            $vendor = $vendorRepo->findOneByUsername($request->request->get('_username'));
            $vendor->setLastSeen(time());
            $this->em->persist($vendor);
            $this->em->flush();
        }
    }
}
