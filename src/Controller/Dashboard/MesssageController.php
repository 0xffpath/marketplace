<?php

namespace App\Controller\Dashboard;

use App\Entity\MailThread;
use App\Form\NewThreadType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Form\MessageType;

class MesssageController extends Controller
{

    /**
     * @Route("/messages/", name="messages")
     * @Route("/staff/messages/", name="staffMessages")
     */
    public function messagesAction(Request $request)
    {
        $mesGen = $this->get('App\Service\Mailer');

        $page = 1;
        if (is_numeric($request->query->get('page'))) {
            $page = $request->query->get('page');
        }

        $threads = $mesGen->getThreads(10, ($page*10)-10);

        $totalpages = ceil(count($threads)/10);

        return $this->render('/dashboard/shared/messages.html.twig', [
            'threads' => $threads,
            'totalPages' => $totalpages,
            'page' => $page,
        ]);
    }

    /**
     * @Route("/messages/new/", name="newMessage")
     * @Route("/staff/messages/new/", name="newStaffMessage")
     */
    public function newMessageAction(Request $request)
    {
        $threadForm = $this->createForm(NewThreadType::class, [], [
            'to' => $request->query->get('username'),
            'subject' => $request->query->get('subject')
        ]);

        $threadForm->handleRequest($request);

        $mesGen = $this->get('App\Service\Mailer');

        if ($threadForm->isSubmitted() && $threadForm->isValid()) {
            $securimage = $this->get('App\Service\Securimage');

            if ($securimage->check($request->request->get('_captcha')) == true) {
                $uuid = \bin2hex(openssl_random_pseudo_bytes(16));

                $mesGen->setThread($threadForm->get('subject')->getData(), $this->getUser()->getUsername(), $uuid);
                $mesGen->setThread($threadForm->get('subject')->getData(), $threadForm->get('to')->getData(), $uuid);

                $mesGen->setMessage($threadForm->get('message')->getData(), $uuid);

                return $this->redirect("/messages/");
            } else {
                $session = new Session();
                $session->getFlashBag()->add('captchaError', "Invalid captcha.");
            }
        }

        return $this->render('/dashboard/shared/newmessage.html.twig', [
            'threadForm' => $threadForm->createView(),
        ]);
    }

    /**
     * @Route("/message/{id}/", name="message")
     * @Route("/staff/message/{id}/", name="staffMessage")
     */
    public function messageAction(Request $request, $id)
    {
        $messageForm = $this->createForm(MessageType::class);
        $messageForm->handleRequest($request);

        $mesGen = $this->get('App\Service\Mailer');

        $messages = $mesGen->getMessages($id);
        $thread = $mesGen->getThread($id);

        if ($messageForm->isSubmitted() && $messageForm->isValid()) {
            $mesGen->setMessage($messageForm->get('message')->getData(), $id);
            $mesGen->setThreadStatus($id, false, false);
            return $this->redirect($request->getRequestUri());
        }

        return $this->render('/dashboard/shared/thread.html.twig', [
            'messages' => $messages,
            'thread' => $thread,
            'messageForm' => $messageForm->createView(),
        ]);
    }

    /**
     * @Route("/message/delete/{id}/", name="deleteMessage")
     * @Route("/staff/message/delete/{id}/", name="staffDeleteMessage")
     */
    public function deleteMessageAction(Request $request, $id)
    {
        $routeName = $this->container->get('request_stack')->getMasterRequest()->get('_route');

        $em = $this->getDoctrine()->getManager();

        $threadRepo = $em->getRepository(MailThread::class);
        $thread = $threadRepo->findOneBy(['uuid' => $id, 'username' => $this->getUser()->getUsername()]);
        $em->remove($thread);
        $em->flush();
        $em->clear();

        if ($routeName == 'staffDeleteMessage') {
            return $this->redirect('/staff/messages/');
        }

        if ($routeName == 'deleteMessage') {
            return $this->redirect('/messages/');
        }
    }

    /**
     * @Route("/messages/readall/", name="readAll")
     * @Route("/staff/messages/readall/", name="staffReadAll")
     */
    public function readAllAction(Request $request)
    {
        $routeName = $this->container->get('request_stack')->getMasterRequest()->get('_route');

        $this->get('App\Service\MessageGenerator')->setThreadReadAll();


        if ($routeName == 'staffReadAll') {
            return $this->redirect('/staff/messages/');
        }

        if ($routeName == 'readAll') {
            return $this->redirect('/messages/');
        }
    }
}
