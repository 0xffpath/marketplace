<?php
namespace App\Service;

// For HD-Wallet Key Derivation
use \BitWasp\Bitcoin\Bitcoin;
use \BitWasp\Bitcoin\Address;
use \BitWasp\Bitcoin\Key\Deterministic\HierarchicalKeyFactory;
use \BitWasp\Buffertools\Buffer;

/*
 * A class that implements HD wallet key/address derivation
 */
class WalletDerive {

    /*
     * Derives child keys/addresses for a given key.
     */
    public function derive_keys($key, $depth) {

        $math = Bitcoin::getMath();
        $network = Bitcoin::getNetwork();

        $master = HierarchicalKeyFactory::fromExtended($key, $network);

        $path = "m/$depth";
        $key = $master->derivePath($path);

        // fixme: hack for copay/multisig.  maybe should use a callback?
        if(method_exists($key, 'getPublicKey')) {
            $pubkey = $key->getPublicKey()->getHex();
        } else {
            throw new Exception("multisig keys not supported");
        }

        return $pubkey;
    }

}