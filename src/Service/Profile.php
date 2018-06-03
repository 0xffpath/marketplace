<?php
namespace App\Service;

use App\Entity\AdminProfile;
use App\Entity\BuyerProfile;
use App\Entity\User;
use App\Entity\VendorProfile;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Profile
{
    private $em;
    private $tokenStorage;

    public function __construct(EntityManager $em, TokenStorageInterface $securityToken)
    {
        $this->em = $em;
        $this->tokenStorage = $securityToken;
    }

    /**
     * @param string $username
     * @return null
     * @throws \Doctrine\ORM\ORMException
     */
    public function getProfile($username = "")
    {
        $profile = null;

        //get user
        if ($username == "") {
            $role = $this->tokenStorage->getToken()->getUser()->getRole();
            $username = $this->tokenStorage->getToken()->getUser()->getUsername();
        } else {
            $userRepo = $this->em->getRepository(User::class);
            $user = $userRepo->findOneByUsername($username);
            $role = $user->getRole();
            $username = $user->getUsername();
        }

        //get the profile from database
        if ($role == 'buyer') {
            $profileRepo = $this->em->getRepository(BuyerProfile::class);
            $profile = $profileRepo->findOneByUsername($username);
        }

        if ($role == 'vendor' || $role == 'new_vendor') {
            $profileRepo = $this->em->getRepository(VendorProfile::class);
            $profile = $profileRepo->findOneByUsername($username);
        }

        if ($role == 'admin') {
            $profileRepo = $this->em->getRepository(AdminProfile::class);
            $profile = $profileRepo->findOneByUsername($username);
        }

        return $profile;
    }

    /**
     * @param string $username
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function getRole($username = "")
    {
        $profile = null;
        if ($username == "") {
            $role = $this->tokenStorage->getToken()->getUser()->getRole();
        } else {
            $userRepo = $this->em->getRepository(User::class);
            $user = $userRepo->findOneByUsername($username);
            $role = $user->getRole();
        }

        return $role;
    }
}
