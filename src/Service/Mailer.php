<?php
namespace App\Service;

use App\Entity\MailMessage;
use App\Entity\MailThread;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Mailer
{
    protected $em;
    private $tokenStorage;
    private $BBCode;

    public function __construct(EntityManager $em, TokenStorageInterface $securityToken, BBCode $BBCode)
    {
        $this->em = $em;
        $this->tokenStorage = $securityToken;
        $this->BBCode = $BBCode;
    }

    /**
     * @param $message string the message the user wants to send
     * @param $uuid string the uuid of the thread
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setMessage($message, $uuid)
    {
        $newMessage = htmlspecialchars($message);
        $newMessage = $this->BBCode->showBBcodes($newMessage);

        $message = new MailMessage();
        $user = $this->tokenStorage->getToken()->getUser()->getUsername();
        $message->setUsername($user);
        $message->setMessage($newMessage);
        $message->setThread($uuid);

        $this->em->persist($message);
        $this->em->flush();
        $this->em->clear();
    }


    /**
     * Gets messages in a thread
     *
     * @param $thread string uuid of thread
     * @return array
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getMessages($thread)
    {
        $messageRepo = $this->em->getRepository(MailMessage::class);
        $messages = $messageRepo->findBy([
            'thread' => $thread,
        ]);

        $this->setThreadStatus($thread, true, true);

        return $messages;
    }

    /**
     * Creates a handle thread
     *
     * @param $subject string subjects of thread
     * @param $order int Id of order
     * @param $username string username of user in thread
     * @param $uuid string uuid of thread
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setThread($subject, $username, $uuid, $order = 0)
    {
        $thread = new MailThread();
        $thread->setSubject($subject);
        $thread->setOrderId($order);
        $thread->setLastMessage(time());
        $thread->setUsername($username);
        $thread->setUUID($uuid);

        $this->em->persist($thread);
        $this->em->flush();
        $this->em->clear();
    }

    /**
     * Fetches list of threads that the user is in
     * @param $thread string uuid of thread
     * @return object
     */
    public function getThread($thread)
    {
        $threadRepo = $this->em->getRepository(MailThread::class);
        $thread = $threadRepo->findOneBy([
            'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
            'uuid' => $thread,
        ]);

        return $thread;
    }

    /**
     * Fetches list of threads that the user is in
     * @param $total int amount of rows to return
     * @param $offset int offset to select
     * @return array
     */
    public function getThreads($total, $offset)
    {
        $threadRepo = $this->em->getRepository(MailThread::class);
        $threads = $threadRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.username = :username and r.orderId = 0')
            ->setParameter('username', $this->tokenStorage->getToken()->getUser()->getUsername())
            ->setFirstResult($offset)
            ->setMaxResults($total)
            ->orderBy('r.id', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return $threads;
    }

    /**
     * Fetches list of unread threads that the user is in
     *
     * @return array
     */
    public function getUnreadThreads()
    {
        $threadRepo = $this->em->getRepository(MailThread::class);
        $threads = $threadRepo->findBy([
            'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
            'seen' => 0,
            'orderId' => 0,
        ]);

        return $threads;
    }


    /**
     * Updates last message of thread
     *
     * @param $id int Id of thread
     * @param $status boolean false is unread, true is read
     * @param $indiv boolean true if set for individual, false if not
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setThreadStatus($id, $status, $indiv)
    {
        $threadRepo = $this->em->getRepository(MailThread::class);

        $threads = [];
        if ($indiv) {
            $threads = $threadRepo->findBy([
                'uuid' => $id,
                'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
            ]);
        } else {
            $threads = $threadRepo->findBy([
                'uuid' => $id,
            ]);
        }

        foreach ($threads as $thread) {
            if ($thread != null) {
                if ($status) {
                    $thread->setSeen(1);
                } elseif (!$status) {
                    $thread->setSeen(0);
                }

                $this->em->merge($thread);
                $this->em->persist($thread);
                $this->em->flush();
                $this->em->clear();
            }
        }
    }
}
