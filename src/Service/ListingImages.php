<?php
namespace App\Service;

use App\Entity\Listing;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ListingImages
{
    protected $em;
    protected $tokenStorage;
    protected $dir;

    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage, $dir)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->dir = $dir;
    }

    /**
     * @param $uuid string uuid of listing
     * @param $offset int number of image to get
     * @return mixed
     */
    public function getImage($uuid, $offset)
    {
        $imageRepo = $this->em->getRepository(\App\Entity\ListingImages::class);
        $images = $imageRepo->createQueryBuilder('r')
            ->select('r.image')
            ->where('r.listing = :listing')
            ->setParameter('listing', $uuid)
            ->getQuery()
            ->getArrayResult();

        if (empty($images[$offset]['image'])) {
            return '404.jpg';
        }
        return $images[$offset]['image'];
    }

    /**
     * @param $uuid string uuid of listing
     * @return array
     */
    public function getImages($uuid)
    {
        $imageRepo = $this->em->getRepository(\App\Entity\ListingImages::class);
        $images = $imageRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.listing = :listing')
            ->setParameter('listing', $uuid)
            ->getQuery()
            ->getArrayResult();

        return $images;
    }


    /**
     * @param $uuid string uuid of listing
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getImageCount($uuid)
    {
        $imageRepo = $this->em->getRepository(\App\Entity\ListingImages::class);
        $count = $imageRepo->createQueryBuilder('r')
            ->select('count(r.image)')
            ->where('r.listing = :listing')
            ->setParameter('listing', $uuid)
            ->getQuery()
            ->getSingleScalarResult();
        return $count;
    }

    public function setImage($image, $uuid)
    {
        $listingRepo = $this->em->getRepository(Listing::class);

        $listing = $listingRepo->findOneBy([
            'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
            'uuid' => $uuid,
        ]);

        if ($image != null) {
            $imageName = md5(time() . rand(1, 1000000)) . '.' . $image->guessExtension();

            $image->move(
                $this->dir,
                $imageName
            );

            $image = new \App\Entity\ListingImages();
            $image->setListing($listing->getUUID());
            $image->setVendor($this->tokenStorage->getToken()->getUser()->getUsername());
            $image->setImage($imageName);

            $this->em->persist($image);
            $this->em->flush();
            $this->em->clear();
        }
    }


    /**
     * @param $uuid string uuid of listing
     * @param $vendor string username of vendor
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeImages($uuid, $vendor)
    {
        $imageRepo = $this->em->getRepository(\App\Entity\ListingImages::class);
        $images = $imageRepo->findBy(['uuid' => $uuid, 'vendor' => $vendor]);

        foreach ($images as $image) {
            unlink($this->dir . '/' . $image->getImage());

            $image = $this->em->merge($image);
            $this->em->remove($image);
        }
        $this->em->flush();
    }


    /**
     * @param $uuid string id of image
     * @param $vendor string username of vendor
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeImage($uuid, $vendor)
    {
        $imageRepo = $this->em->getRepository(\App\Entity\ListingImages::class);
        $image = $imageRepo->findOneBy(['uuid' => $uuid, 'vendor' => $vendor]);

        unlink($this->dir . '/' . $image->getImage());

        $image = $this->em->merge($image);
        $this->em->remove($image);
        $this->em->flush();
    }
}
