<?php
namespace Nistruct\ContactForm\Ui\DataProvider;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;

class Collection extends SearchResult
{
    protected $logger;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable,
        $resourceModel
    ) {
        // Postavljanje logger instance
        $this->logger = $logger;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    protected function _initSelect()
    {
        if ($this->logger) {
            $this->logger->info('Početak izvršenja _initSelect metode');
        }

        try {
            $this->addFilterToMap('entity_id', 'entity_id');
            $this->addFilterToMap('name', 'name');
            $this->addFilterToMap('email', 'email');
            $this->addFilterToMap('message', 'message');
            $this->addFilterToMap('created_at', 'created_at');

            // Logovanje SQL upita pre poziva parent metode
            if ($this->logger) {
                $this->logger->info('SQL upit pre parent::_initSelect(): ' . $this->getSelect()->__toString());
            }

            parent::_initSelect();

            // Logovanje SQL upita posle poziva parent metode
            if ($this->logger) {
                $this->logger->info('SQL upit posle parent::_initSelect(): ' . $this->getSelect()->__toString());
            }

            if ($this->logger) {
                $this->logger->info('Filteri su uspešno dodati');
            }

        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Greška u _initSelect metodi: ' . $e->getMessage());
            }
        }
    }
}
