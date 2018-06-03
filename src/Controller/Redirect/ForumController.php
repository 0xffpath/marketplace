<?php
namespace App\Controller\Redirect;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ForumController extends Controller
{

    /**
     * @Route("/forum/", name="forum")
     */
    public function forumAction()
    {
        return $this->redirect('/exit?market&url=' . $this->getParameter('forum'));
    }
}
