<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ListingDisable
{
    protected $em;
    protected $tokenStorage;

    public function __construct(EntityManager $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * Checks if the creation of listings should be disabled
     * Disable if no BTC, XMR, ZEC, or PGP
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function check()
    {
        $profile = new Profile($this->em, $this->tokenStorage);
        $profile = $profile->getProfile();

        if (
            $profile->getBTCPublic() == null
            && $profile->getXMRAddress() == null
            && $profile->getZECAddress() == null
            || $profile->getPGP() == null
        ) {
            return true;
        }
        return false;
    }
}
