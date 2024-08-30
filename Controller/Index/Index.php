<?php
namespace Nistruct\ContactForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page; // Ispravan import za Page

class Index extends Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = __('Nistruct Contact Form');
        $resultPage->getConfig()->getTitle()->set($title);
        return $resultPage;
    }
}
