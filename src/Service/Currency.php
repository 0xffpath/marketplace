<?php
namespace App\Service;

use App\Entity\CryptoPrice;
use Doctrine\ORM\EntityManager;

class Currency
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Gets what one unit of fiat equals in BTC
     *
     * @return array BTC prices per unit of fiat
     */
    public function getBTC()
    {
        $currencies = ['AUD',
            'CAD',
            'CHF',
            'EUR',
            'GBP',
            'NZD',
            'USD',];

        $cryptoRepo = $this->em->getRepository(CryptoPrice::class);

        $rawPrices = $cryptoRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.fiat in(:fiat) and r.crypto = 'BTC'")
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(14)
            ->setParameter('fiat', $currencies, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();

        $prices = [];
        foreach ($rawPrices as $price) {
            $prices[$price['fiat']] = round(1/$price['price'], 10);
        }
        return $prices;
    }

    /**
     * Gets what one unit of BTC equals in fiat
     *
     * @return array Fiat price of BTC
     */
    public function getFiatBTC()
    {
        $currencies = ['AUD',
            'CAD',
            'CHF',
            'EUR',
            'GBP',
            'NZD',
            'USD',];

        $cryptoRepo = $this->em->getRepository(CryptoPrice::class);

        $rawPrices = $cryptoRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.fiat in(:fiat) and r.crypto = 'BTC'")
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(14)
            ->setParameter('fiat', $currencies, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();

        $prices = [];
        foreach ($rawPrices as $price) {
            $prices[$price['fiat']] = $price['price'];
        }

        return $prices;
    }

    /**
     * Gets what one unit of fiat equals in XMR
     *
     * @return array XMR prices per unit of fiat
     */
    public function getXMR()
    {
        $currencies = ['AUD',
            'CAD',
            'CHF',
            'EUR',
            'GBP',
            'NZD',
            'USD',];

        $cryptoRepo = $this->em->getRepository(CryptoPrice::class);

        $rawPrices = $cryptoRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.fiat in(:fiat) and r.crypto = 'XMR'")
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(14)
            ->setParameter('fiat', $currencies, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();

        $prices = [];
        foreach ($rawPrices as $price) {
            $prices[$price['fiat']] = round(1/$price['price'], 10);
        }
        return $prices;
    }

    /**
     * Gets what one unit of XMR equals in fiat
     *
     * @return array Fiat price of XMR
     */
    public function getFiatXMR()
    {
        $currencies = ['AUD',
            'CAD',
            'CHF',
            'EUR',
            'GBP',
            'NZD',
            'USD',];

        $cryptoRepo = $this->em->getRepository(CryptoPrice::class);

        $rawPrices = $cryptoRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.fiat in(:fiat) and r.crypto = 'XMR'")
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(14)
            ->setParameter('fiat', $currencies, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();

        $prices = [];
        foreach ($rawPrices as $price) {
            $prices[$price['fiat']] = $price['price'];
        }

        return $prices;
    }

    /**
     * Gets what one unit of fiat equals in ZEC
     *
     * @return array ZEC prices per unit of fiat
     */
    public function getZEC()
    {
        $currencies = ['AUD',
            'CAD',
            'CHF',
            'EUR',
            'GBP',
            'NZD',
            'USD',];

        $cryptoRepo = $this->em->getRepository(CryptoPrice::class);

        $rawPrices = $cryptoRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.fiat in(:fiat) and r.crypto = 'ZEC'")
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(14)
            ->setParameter('fiat', $currencies, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();

        $prices = [];
        foreach ($rawPrices as $price) {
            $prices[$price['fiat']] = round(1/$price['price'], 10);
        }
        return $prices;
    }

    /**
     * Gets what one unit of ZEC equals in fiat
     *
     * @return array Fiat price of ZEC
     */
    public function getFiatZEC()
    {
        $currencies = ['AUD',
            'CAD',
            'CHF',
            'EUR',
            'GBP',
            'NZD',
            'USD',];

        $cryptoRepo = $this->em->getRepository(CryptoPrice::class);

        $rawPrices = $cryptoRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.fiat in(:fiat) and r.crypto = 'ZEC'")
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(14)
            ->setParameter('fiat', $currencies, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();

        $prices = [];
        foreach ($rawPrices as $price) {
            $prices[$price['fiat']] = $price['price'];
        }

        return $prices;
    }

    /**
     * Gets what one unit of BTC, ZEC, XMR equals in fiat
     *
     * @return array Fiat price of BTC, ZEC, XMR
     */
    public function getAll()
    {
        $currencies = ['AUD',
            'CAD',
            'CHF',
            'EUR',
            'GBP',
            'NZD',
            'USD',];

        $cryptoRepo = $this->em->getRepository(CryptoPrice::class);

        $rawPrices = $cryptoRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.fiat in(:fiat)")
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(100)
            ->setParameter('fiat', $currencies, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();

        $prices = [];
        foreach ($rawPrices as $price) {
            $prices[$price['crypto']][$price['fiat']] = round(1/$price['price'], 10);
        }
        return $prices;
    }

    /**
     * Gets what one unit of BTC, XMR, ZEC equals in fiat
     *
     * @return array Fiat price of BTC, XMR, ZEC
     */
    public function getFiatAll()
    {
        $currencies = ['AUD',
            'CAD',
            'CHF',
            'EUR',
            'GBP',
            'NZD',
            'USD',];

        $cryptoRepo = $this->em->getRepository(CryptoPrice::class);

        $rawPrices = $cryptoRepo->createQueryBuilder('r')
            ->select('r')
            ->where("r.fiat in(:fiat)")
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(100)
            ->setParameter('fiat', $currencies, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();


        $prices = [];
        foreach ($rawPrices as $price) {
            $prices[$price['crypto']][$price['fiat']] = $price['price'];
        }

        return $prices;
    }
}
