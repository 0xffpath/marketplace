<?php
namespace App\Service\Wallet;

class WalletFactory
{

    /**
     * @param $type
     * @return Wallet
     */
    public function create($type)
    {
        return new Wallet($type);
    }
}