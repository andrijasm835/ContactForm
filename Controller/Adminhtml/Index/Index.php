<?php
namespace Nistruct\ContactForm\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    protected $resultPageFactory;
    protected $logger;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        // Logovanje
        $this->logger->info('Index action executed');

        // Postavlja naslov stranice
        $resultPage->getConfig()->getTitle()->prepend(__('Nistruct Contact Form Submissions'));

        return $resultPage;
    }
}

