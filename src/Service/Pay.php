<?php
namespace App\Service;
use App\Entity\Orders;
use App\Service\Wallet\WalletFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Pay
{
    protected $em;
    private $tokenStorage;
    private $wallet;

    public function __construct(EntityManager $em, TokenStorageInterface $securityToken, WalletFactory $wallet)
    {
        $this->em = $em;
        $this->tokenStorage = $securityToken;
        $this->wallet = $wallet;
    }


    /**
     * @param $uuid
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setPaid($uuid)
    {
        $ordersRepo = $this->em->getRepository(Orders::class);
        $order = $ordersRepo->findOneByUuid($uuid);

        $confirmed = 0;
        if($order->getCrypto() == 'btc'){
            $bitcoin = $this->wallet->create('bitcoin');
            $confirmed = $bitcoin->getaddressbalance($order->getAddress())['confirmed'];
        }

        if($order->getRecieved() && !$order->getConfirmed() && $confirmed >= $order->getCryptoTotal()){
            $order->setConfirmed(true);
            $order->setStatus('pending');
            $order->setBootstrap('primary');
            $this->em->persist($order);
            $this->em->flush();
            $this->em->clear();
        }
    }
}