<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Experience
{
    protected $em;
    protected $profile;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, Profile $profile)
    {
        $this->em = $em;
        $this->profile = $profile;
    }

    /**
     * Adds experience, and updates level
     *
     * @param string $username username to update, default to current user
     * @param int $experience experience to add
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update($username = "", $experience = 0)
    {
        if (!empty($username)) {
            $profile = $this->profile->getProfile($username);
        } else {
            $profile = $this->profile->getProfile();
        }

        $profile->setExperience($profile->getExperience() + $experience);

        $experience = $profile->getExperience();
        switch (true) {
            case $experience < 4000:
                $profile->setLevel(1);
                break;
            case  $experience >= 4000 && $experience < 9000:
                $profile->setLevel(2);
                break;
            case  $experience >= 9000 && $experience < 16000:
                $profile->setLevel(3);
                break;
            case  $experience >= 16000 && $experience < 25000:
                $profile->setLevel(4);
                break;
            case  $experience >= 25000 && $experience < 36000:
                $profile->setLevel(5);
                break;
            case  $experience >= 36000 && $experience < 49000:
                $profile->setLevel(6);
                break;
            case  $experience >= 49000 && $experience < 64000:
                $profile->setLevel(7);
                break;
            case  $experience >= 64000 && $experience < 81000:
                $profile->setLevel(8);
                break;
            case  $experience >= 81000:
                $profile->setLevel(9);
                break;
            default:
                throw new NotFoundHttpException('Error processing experience.');
        }

        $this->em->persist($profile);
        $this->em->flush();
        $this->em->clear();
    }
}
