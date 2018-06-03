<?php
namespace App\Cron;

use Symfony\Component\Dotenv\Dotenv;

require_once(__DIR__ . '/../../vendor/symfony/dotenv/Dotenv.php');

/**
 * Deletes messages and orders after one month
 *
 * Improve efficiency in future.
 */
class Database
{
    private $db;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../.env');

        $this->db = new \PDO("mysql:host=" . getenv('DB_HOST') . ";port=" . getenv('DB_PORT') . ";dbname=" . getenv('DB_NAME') . ";charset=utf8mb4", getenv('DB_USER'), getenv('DB_PASS'));
    }

    /**
     * Removes all orders that are 30 days old after the auto finalization.
     */
    public function removeOrders()
    {
        //30 days
        $time = time() - (30*60*60*24);
        $stmt = $this->db->prepare("SELECT * FROM `orders` WHERE `auto_date` < :one_month");
        $stmt->execute([':one_month' => $time]);
        $orders = $stmt->fetchAll();

        foreach ($orders as $order) {
            $stmt = $this->db->prepare("DELETE FROM `orders` WHERE `id` = :id");
            $stmt->execute([':id', $order['id']]);
        }
    }

    /**
     * Removes all messages that are inactive for 30 days
     */
    public function removeMessages()
    {

        //30 days ago
        $time = time() - (30*60*60*24);
        $stmt = $this->db->prepare("SELECT * FROM `mail_thread` WHERE `last_message` < :one_month");
        $stmt->execute([':one_month' => $time]);
        $threads = $stmt->fetchAll();

        foreach ($threads as $thread) {
            $stmt = $this->db->prepare("DELETE FROM `mail_thread` WHERE `id` = :id");
            $stmt->execute([':id' => $thread['id']]);

            $stmt = $this->db->prepare("DELETE FROM `mail_message` WHERE `thread` = :uuid");
            $stmt->execute([':uuid' => $thread['uuid']]);
        }
    }
}

$database = new Database();
$database->removeOrders();
$database->removeMessages();
