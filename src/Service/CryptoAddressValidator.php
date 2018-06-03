<?php
namespace App\Service;

class CryptoAddressValidator
{
    public function validateBTCAddress($address)
    {
        $decoded = $this->decodeBase58($address);

        if ($decoded == false) {
            return false;
        }

        $d1 = hash("sha256", substr($decoded, 0, 21), true);
        $d2 = hash("sha256", $d1, true);

        if (substr_compare($decoded, $d2, 21, 4)) {
            return false;
        }
        return true;
    }

    public function validateBTCMaster($key)
    {
        if (!preg_match('/^(xpub)?[0-9A-Za-z]{111}/i', $key)) {
            return false;
        }
        return true;
    }

    public function validateBTCPublic($key, $address)
    {
        if (strlen($address) !== 0) {
            $publickey=$key;
            $step1=$this->hexStringToByteString($publickey);
            $step2=hash("sha256", $step1);
            $step3=hash('ripemd160', $this->hexStringToByteString($step2));
            $step4="00".$step3;
            $step5=hash("sha256", $this->hexStringToByteString($step4));
            $step6=hash("sha256", $this->hexStringToByteString($step5));
            $checksum=substr($step6, 0, 8);
            $step8=$step4.$checksum;
            $step9="1".$this->bc_base58_encode($this->bc_hexdec($step8));
            if ($step9 === $address) {
                return true;
            }
            return false;
        }

        if (strlen($address) === 0) {
            if (strlen($key) === 66 && (substr($key, 0, 2) === '02' || substr($key, 0, 2) === '03')) {
                return true;
            }
            if (strlen($key) === 130 && substr($key, 0, 2) === '04') {
                return true;
            }
            return false;
        }
    }

    public function validateXMRAddress($address)
    {
        if (!preg_match('/^(4)?[0-9A-B]?[0-9A-Za-z]{95}/i', $address)) {
            return false;
        }
        return true;
    }

    public function validateZECAddress($address)
    {
        if (!preg_match('/^(z)?[0-9A-Za-z]{95}/i', $address)) {
            return false;
        }
        return true;
    }

    public function hexStringToByteString($hexString)
    {
        $len=strlen($hexString);

        $byteString="";
        for ($i=0;$i<$len;$i=$i+2) {
            $charnum=hexdec(substr($hexString, $i, 2));
            $byteString.=chr($charnum);
        }

        return $byteString;
    }

    public function bc_arb_encode($num, $basestr)
    {
        if (! function_exists('bcadd')) {
            throw new \Exception('You need the BCmath extension.');
        }

        $base = strlen($basestr);
        $rep = '';

        while (true) {
            if (strlen($num) < 2) {
                if (intval($num) <= 0) {
                    break;
                }
            }
            $rem = bcmod($num, $base);
            $rep = $basestr[intval($rem)] . $rep;
            $num = bcdiv(bcsub($num, $rem), $base);
        }
        return $rep;
    }

    public function bc_arb_decode($num, $basestr)
    {
        if (! function_exists('bcadd')) {
            throw new \Exception('You need the BCmath extension.');
        }

        $base = strlen($basestr);
        $dec = '0';

        $num_arr = str_split((string)$num);
        $cnt = strlen($num);
        for ($i=0; $i < $cnt; $i++) {
            $pos = strpos($basestr, $num_arr[$i]);
            if ($pos === false) {
                throw new \Exception(sprintf('Unknown character %s at offset %d', $num_arr[$i], $i));
            }
            $dec = bcadd(bcmul($dec, $base), $pos);
        }
        return $dec;
    }

    public function bc_base58_encode($num)
    {
        return $this->bc_arb_encode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

    public function bc_base58_decode($num)
    {
        return $this->bc_arb_decode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

    public function bc_hexdec($num)
    {
        return $this->bc_arb_decode(strtolower($num), '0123456789abcdef');
    }

    public function bc_dechex($num)
    {
        return bc_arb_encode($num, '0123456789abcdef');
    }

    public function decodeBase58($input)
    {
        $alphabet = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";

        $out = array_fill(0, 25, 0);
        for ($i=0;$i<strlen($input);$i++) {
            if (($p=strpos($alphabet, $input[$i]))===false) {
                return false;
            }
            $c = $p;
            for ($j = 25; $j--;) {
                $c += (int)(58 * $out[$j]);
                $out[$j] = (int)($c % 256);
                $c /= 256;
                $c = (int)$c;
            }
            if ($c != 0) {
                return false;
            }
        }

        $result = "";
        foreach ($out as $val) {
            $result .= chr($val);
        }

        return $result;
    }
}
