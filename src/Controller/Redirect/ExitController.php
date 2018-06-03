<?php

namespace App\Controller\Redirect;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExitController extends Controller
{
    /**
     * @Route("/exit", name="exit")
     */
    public function exitAction(Request $request)
    {
        $return = $request->query->get('referer');

        if ($return == "") {
            $return = "/";
        }
        return $this->render('/exit.html.twig', [
            'url' => $request->query->get('url'),
            'return' => $return,
        ]);
    }
}
