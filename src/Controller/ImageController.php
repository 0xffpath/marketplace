<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ImageController extends Controller
{

    /**
     * @Route("/image/remove/{product}/{id}/", name="removeImage")
     */
    public function removeImageAction(Request $request, $product, $id)
    {
        if ($this->get('App\Service\ListingImages')->getImageCount($product) > 1) {
            $this->get('App\Service\ListingImages')->removeImage($id, $this->getUser()->getUsername());
        }
        //bug with symfony, remove '?' with new version
        return $this->redirect(urldecode($request->query->get('?return')));
    }
}
