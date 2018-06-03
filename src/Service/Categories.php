<?php
namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Categories
{
    protected $em;
    private $tokenStorage;

    public function __construct(EntityManager $em, TokenStorageInterface $securityToken)
    {
        $this->em = $em;
        $this->tokenStorage = $securityToken;
    }

    /**
     * Returns category with subcategory by Id of sub
     *
     * @param $sub integer Id of subcategory
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function getBySub($sub)
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $categories = $categoryRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.sub = :sub')
            ->setParameter('sub', 1)
            ->getQuery()
            ->getArrayResult();

        $all = ['categories' => $categories, 'sub' => $categoryRepo->findByParentId($sub)];

        return $all;
    }

    /**
     * Returns categories without sub
     *
     * @return array
     */
    public function getCategories()
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $categories = $categoryRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.sub = :sub')
            ->setParameter('sub', 1)
            ->getQuery()
            ->getArrayResult();

        return ['categories' => $categories, 'sub' => []];
    }

    /**
     * Returns both categories and subcategories
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function getAll()
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $categories = $categoryRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.sub = :sub')
            ->setParameter('sub', 1)
            ->getQuery()
            ->getArrayResult();

        $all = [];
        foreach ($categories as $category) {
            $all[$category['title']] = [$category, $categoryRepo->findByParentId($category['id'])];
        }

        return $all;
    }

    /**
     * Get category name from Id
     *
     * @param $category
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function categoryName($category)
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $category = $categoryRepo->findOneById($category);
        if ($category == null) {
            throw new NotFoundHttpException('Category Not Found');
        }
        return $category->getTitle();
    }


    /**
     * Add to item total of category
     *
     * @param $category
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addItem($category)
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $category = $categoryRepo->findOneById($category);
        $itemTotal = $category->getItemTotal()+1;
        $category->setItemTotal($itemTotal);
        $this->em->merge($category);
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * Remove from item total of category
     *
     * @param $id
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeItem($id)
    {
        $categoryRepo = $this->em->getRepository(Category::class);
        $category = $categoryRepo->findOneById($id);
        $itemTotal = $category->getItemTotal()-1;
        $category->setItemTotal($itemTotal);
        $this->em->merge($category);
        $this->em->flush();
        $this->em->clear();
    }
}
