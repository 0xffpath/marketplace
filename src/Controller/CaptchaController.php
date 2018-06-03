<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CaptchaController extends Controller
{
    /**
     * @Route("/img/captcha3/", name="captcha")
     */
    public function captchaAction(Request $request)
    {
        $securimage = $this->get('App\Service\Securimage');
        return new Response($securimage->show());
    }
}
