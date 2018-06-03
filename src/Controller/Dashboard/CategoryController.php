<?php

namespace App\Controller\Dashboard;

use App\Entity\Category;
use App\Form\Admin\CategoriesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class CategoryController extends Controller
{

    /**
     * @Route("/staff/categories/", name="categories")
     */
    public function categoriesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $cat[''] = '';

        foreach ($this->get('App\Service\Categories')->getCategories()['categories'] as $category) {
            $cat[$category['title']] = $category['id'];
        }

        $categoryForm = $this->createForm(CategoriesType::class, [], [
            'parent' => $cat,
        ]);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $categoryRepo = $em->getRepository(Category::class);
            $check = $categoryRepo->findByTitle($categoryForm->get('title')->getData());

            if (empty($categoryForm->get('parent')->getData()) && count($check) != 0) {
                $session = new Session();
                $session->getFlashBag()->add('error', 'This is a duplicate category.');
                return $this->redirectToRoute('categories');
            }

            $category = new Category();
            $category->setTitle($categoryForm->get('title')->getData());
            if (!empty($categoryForm->get('parent')->getData())) {
                $category->setParentId($categoryForm->get('parent')->getData());
                $category->setSub(0);
            }
            $em->persist($category);
            $em->flush();
            $em->clear();

            return $this->redirectToRoute('categories');
        }

        return $this->render('/dashboard/admin/categories.html.twig', [
            'categoryForm' => $categoryForm->createView(),
        ]);
    }

    /**
     * @Route("/staff/categories/delete/{id}", name="deleteCategories")
     */
    public function categoriesDeleteAction(Request $request, $id)
    {
        if ($this->getUser()->getRole() == 'admin') {
            $em = $this->getDoctrine()->getManager();

            $categoriesRepo = $em->getRepository(Category::class);
            $categories = $categoriesRepo->findOneById($id);
            $em->remove($categories);
            $em->flush();

            return $this->redirect('/staff/categories/');
        }
    }
}
