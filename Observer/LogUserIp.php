<?php
namespace Nistruct\ContactForm\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress; // Dodano za pristup IP adresi

class LogUserIp implements ObserverInterface
{
    protected $request;
    protected $logger;
    protected $directoryList;
    protected $remoteAddress; // Dodano za pristup IP adresi

    public function __construct(
        RequestInterface $request,
        Monolog $logger,
        DirectoryList $directoryList,
        RemoteAddress $remoteAddress // Dodano za pristup IP adresi
    ) {
        $this->request = $request;
        $this->logger = $logger;
        $this->directoryList = $directoryList;
        $this->remoteAddress = $remoteAddress; // Dodano za pristup IP adresi
    }

    public function execute(Observer $observer)
    {
        // Get user IP address
        $ipAddress = $this->remoteAddress->getRemoteAddress(); // Koristite getRemoteAddress() za IP adresu
        $filePath = $this->directoryList->getPath(DirectoryList::VAR_DIR) . '/log/ip-address-log.txt';
        $logEntry = sprintf("User IP Address: %s - Timestamp: %s", $ipAddress, date('Y-m-d H:i:s')) . PHP_EOL;

        try {
            file_put_contents($filePath, $logEntry, FILE_APPEND);
            $this->logger->info($logEntry);
        } catch (\Exception $e) {
            $this->logger->error('Failed to write IP address to log file: ' . $e->getMessage());
        }
    }
}
