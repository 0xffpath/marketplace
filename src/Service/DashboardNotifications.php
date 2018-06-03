<?php
namespace App\Service;

use App\Entity\MailThread;
use App\Entity\Notification;
use App\Entity\Orders;
use App\Entity\Report;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DashboardNotifications
{
    protected $em;
    private $tokenStorage;

    public function __construct(EntityManager $em, TokenStorageInterface $securityToken)
    {
        $this->em = $em;
        $this->tokenStorage = $securityToken;
    }

    /**
     * Gets total reports
     *
     * @return int total reports
     */
    public function getReportTotal()
    {
        if ($this->tokenStorage->getToken()->getUser()->getRole() == 'admin') {
            $reportRepo = $this->em->getRepository(Report::class);
            $reports = $reportRepo->findAll();
            return count($reports);
        }
        throw new AccessDeniedException('You do not have permission.');
    }

    /**
     * Gets total disputes
     *
     * @return int total disputes
     * @throws \Doctrine\ORM\ORMException
     */
    public function getDisputeTotal()
    {
        if ($this->tokenStorage->getToken()->getUser()->getRole() == 'admin') {
            $ordersRepo = $this->em->getRepository(Orders::class);
            $orders = $ordersRepo->findByStatus('disputed');
            return count($orders);
        }
        throw new AccessDeniedException('You do not have permission.');
    }

    /**
     * Gets total new messages
     *
     * @return int total new messages
     */
    public function getNewMessageTotal()
    {
        $messageRepo = $this->em->getRepository(MailThread::class);
        $count = $messageRepo->findBy([
            'username' => $this->tokenStorage->getToken()->getUser()->getUsername(),
            'seen' => 0,
            'orderId' => 0,
        ]);

        return count($count);
    }

    /**
     * Gets total orders that are still pending
     *
     * @return int total new orders
     */
    public function getNewOrderTotal()
    {
        $ordersRepo = $this->em->getRepository(Orders::class);
        $total = $ordersRepo->findBy(['vendor' => $this->tokenStorage->getToken()->getUser()->getUsername(), 'status' => 'pending']);
        return count($total);
    }

    /**
     * Gets total notifications not seen
     *
     * @return int total new notifications
     */
    public function getNewNotificationTotal()
    {
        $notificationRepo = $this->em->getRepository(Notification::class);
        $total = $notificationRepo->createQueryBuilder('r')
            ->select('r')
            ->where('r.username = :username')
            ->setParameter('username', $this->tokenStorage->getToken()->getUser()->getUsername())
            ->getQuery()
            ->getArrayResult();
        return count($total);
    }

    /**
     * Gets total orders
     *
     * @return int total orders
     * @throws \Doctrine\ORM\ORMException
     */
    public function getTotalOrders()
    {
        $ordersRepo = $this->em->getRepository(Orders::class);

        if ($this->tokenStorage->getToken()->getUser()->getRole() == 'buyer') {
            $total = $ordersRepo->findByBuyer($this->tokenStorage->getToken()->getUser()->getUsername());
        }
        if ($this->tokenStorage->getToken()->getUser()->getRole() == 'vendor') {
            $total = $ordersRepo->findByVendor($this->tokenStorage->getToken()->getUser()->getUsername());
        }

        return count($total);
    }

    /**
     * Gets array of order statuses
     *
     * @return mixed status of orders
     */
    public function getOrderStatus()
    {
        $ordersRepo = $this->em->getRepository(Orders::class);

        $orders[] = [];

        if ($this->tokenStorage->getToken()->getUser()->getRole() == 'buyer') {
            $orders = $ordersRepo->createQueryBuilder('r')
                ->select('r.status')
                ->where('r.buyer = :buyer')
                ->setParameter('buyer', $this->tokenStorage->getToken()->getUser()->getUsername())
                ->getQuery()
                ->getArrayResult();
        }
        if ($this->tokenStorage->getToken()->getUser()->getRole() == 'vendor') {
            $orders = $ordersRepo->createQueryBuilder('r')
                ->select('r.status')
                ->where('r.vendor = :vendor')
                ->setParameter('vendor', $this->tokenStorage->getToken()->getUser()->getUsername())
                ->getQuery()
                ->getArrayResult();
        }

        $status['confirmation'] = $status['pending']
            = $status['accepted'] = $status['rejected']
            = $status['timeout'] = $status['shipped']
            = $status['canceled'] = $status['finalized']
            = $status['disputed'] = 0;

        foreach ($orders as $order) {
            switch ($order['status']) {
                case 'confirmation':
                    $status['confirmation']++;
                    break;
                case 'pending':
                    $status['pending']++;
                    break;
                case 'accepted':
                    $status['accepted']++;
                    break;
                case 'rejected':
                    $status['rejected']++;
                    break;
                case 'timeout':
                    $status['timeout']++;
                    break;
                case 'shipped':
                    $status['shipped']++;
                    break;
                case 'canceled':
                    $status['canceled']++;
                    break;
                case 'auto' || 'finalized':
                    $status['finalized']++;
                    break;
                case 'disputed':
                    $status['disputed']++;
                    break;
                case 'finalized':
                    $status['finalized']++;
                    break;
            }
        }
        return $status;
    }
}
