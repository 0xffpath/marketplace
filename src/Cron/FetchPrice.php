<?php
namespace App\Cron;

use Symfony\Component\Dotenv\Dotenv;

require_once(__DIR__ . '/../../vendor/symfony/dotenv/Dotenv.php');

class FetchPrice
{
    private $db;

    /**
     * FetchPrice constructor.
     * Deletes all rows except past 100.
     */
    public function __construct()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');

        $this->db = new \PDO("mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4", getenv('DB_USER'), getenv('DB_PASS'));
        $stmt = $this->db->prepare("DELETE FROM `crypto_price` WHERE id <= (SELECT id FROM (SELECT id FROM `crypto_price` ORDER BY id DESC LIMIT 1 OFFSET 100) foo)");
        $stmt->execute();
    }

    public function fetchBTCPrice()
    {
        $currencies =
            ['AUD',
                'CAD',
                'CHF',
                'EUR',
                'GBP',
                'NZD',
                'USD'];

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'https://min-api.cryptocompare.com/data/price?fsym=BTC&tsyms=AUD,CAD,CHF,EUR,GBP,NZD,USD');
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        $cryptoCompare = json_decode(curl_exec($curl_handle), true);

        $time = time();

        //insert each of the crypto into the database
        foreach ($currencies as $currency) {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, 'https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=' . $currency);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            $coinMarket = json_decode(curl_exec($curl_handle), true)[0]['price_' . strtolower($currency)];

            if ($cryptoCompare[$currency] != 0 && $coinMarket != 0) {
                $price = ($cryptoCompare[$currency] + $coinMarket)/2;
                $stmt = $this->db->prepare("INSERT INTO crypto_price(`crypto`, `fiat`, `price`, `time`) VALUES(:crypto,:fiat,:price,:time)");
                $stmt->execute(array(':crypto' => 'BTC', ':fiat' => $currency, ':price' => $price, ':time' => $time));
            }
        }

        curl_close($curl_handle);
    }

    public function fetchXMRPrice()
    {
        $currencies =
            ['AUD',
                'CAD',
                'CHF',
                'EUR',
                'GBP',
                'NZD',
                'USD'];

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'https://min-api.cryptocompare.com/data/price?fsym=XMR&tsyms=AUD,CAD,CHF,EUR,GBP,NZD,USD');
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        $cryptoCompare = json_decode(curl_exec($curl_handle), true);

        $time = time();

        //insert each of the crypto into the database
        foreach ($currencies as $currency) {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, 'https://api.coinmarketcap.com/v1/ticker/monero/?convert=' . $currency);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            $coinMarket = json_decode(curl_exec($curl_handle), true)[0]['price_' . strtolower($currency)];

            if ($cryptoCompare[$currency] != 0 && $coinMarket != 0) {
                $price = ($cryptoCompare[$currency] + $coinMarket)/2;
                $stmt = $this->db->prepare("INSERT INTO crypto_price(`crypto`, `fiat`, `price`, `time`) VALUES(:crypto,:fiat,:price,:time)");
                $stmt->execute(array(':crypto' => 'XMR', ':fiat' => $currency, ':price' => $price, ':time' => $time));
            }
        }

        curl_close($curl_handle);
    }

    public function fetchZECPrice()
    {
        $currencies =
            ['AUD',
                'CAD',
                'CHF',
                'EUR',
                'GBP',
                'NZD',
                'USD'];

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'https://min-api.cryptocompare.com/data/price?fsym=ZEC&tsyms=AUD,CAD,CHF,EUR,GBP,NZD,USD');
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        $cryptoCompare = json_decode(curl_exec($curl_handle), true);

        $time = time();

        //insert each of the crypto into the database
        foreach ($currencies as $currency) {
            $curl_handle = curl_init();
            curl_setopt($curl_handle, CURLOPT_URL, 'https://api.coinmarketcap.com/v1/ticker/zcash/?convert=' . $currency);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            $coinMarket = json_decode(curl_exec($curl_handle), true)[0]['price_' . strtolower($currency)];

            if ($cryptoCompare[$currency] != 0 && $coinMarket != 0) {
                $price = ($cryptoCompare[$currency] + $coinMarket)/2;
                $stmt = $this->db->prepare("INSERT INTO crypto_price(`crypto`, `fiat`, `price`, `time`) VALUES(:crypto,:fiat,:price,:time)");
                $stmt->execute(array(':crypto' => 'ZEC', ':fiat' => $currency, ':price' => $price, ':time' => $time));
            }
        }

        curl_close($curl_handle);
    }
}

$currency = new FetchPrice();
$currency->fetchBTCPrice();
$currency->fetchXMRPrice();
$currency->fetchZECPrice();
