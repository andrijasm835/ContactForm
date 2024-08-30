<?php

namespace Nistruct\ContactForm\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Nistruct\ContactForm\Model\ContactFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Post extends Action
{
    protected $contactFactory;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $dateTime;
    protected $storeManager;
    protected $logger;
    protected $eventManager;

    public function __construct(
        Context $context,
        ContactFactory $contactFactory,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        DateTime $dateTime,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        EventManager $eventManager
    ) {
        $this->contactFactory = $contactFactory;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->eventManager = $eventManager;
        parent::__construct($context);
    }

    public function execute()
{
    $post = $this->getRequest()->getParams();
    $this->_writeLog('POST Data Received: ' . print_r($post, true));

    if (!empty($post)) {
        try {
            if (!isset($post['name'], $post['email'], $post['message'])) {
                throw new \Exception(__('Missing required POST data.'));
            }

            $contact = $this->contactFactory->create();
            $contact->setData([
                'name' => $post['name'],
                'email' => $post['email'],
                'message' => $post['message'],
                'created_at' => $this->dateTime->gmtDate()
            ]);
            $contact->save();

            $post['date'] = $this->dateTime->gmtDate();
            $this->_sendEmail($post);

            $this->eventManager->dispatch('nistruct_contact_form', ['request' => $this->getRequest()]);

            $this->messageManager->addSuccessMessage(__('Thank you for submitting to the Nistruct contact form!'));

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('/');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred: ') . $e->getMessage());

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('/');
            return $resultRedirect;
        }
    }

    /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    $resultRedirect->setPath('/');
    return $resultRedirect;
}





protected function _sendEmail($postData)
{
    try {
        // Proverite i izostavite form_key ako postoji
        if (isset($postData['form_key'])) {
            unset($postData['form_key']);
        }

        $recipientEmail = $this->scopeConfig->getValue(
            'nistruct_contactform/contact_form_settings/email_recipient',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception(__('Invalid email address configured.'));
        }

        // Proverite varijable i generišite datum ako nije dostupan
        $name = isset($postData['name']) ? (string)$postData['name'] : '';
        $email = isset($postData['email']) ? (string)$postData['email'] : '';
        $message = isset($postData['message']) ? (string)$postData['message'] : '';
        $date = isset($postData['date']) ? (string)$postData['date'] : date('Y-m-d H:i:s');

        $this->_writeLog('Sending email via SMTP to: ' . $recipientEmail);
        $this->_writeLog('Template Vars Before Sending Email: ' . print_r([
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'date' => $date
        ], true));

        $transportBuilder = $this->transportBuilder
            ->setTemplateIdentifier('nistruct_contactform_email_template')
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()
            ])
            ->setTemplateVars([
                'name' => (string) $name,
                'email' => (string) $email,
                'message' => (string) $message,
                'date' => (string) $date
            ])
            ->setFrom([
                'email' => 'andrija.smiljkovic@nistruct.com',
                'name' => 'Nistruct Contact Form'
            ])
            ->addTo($recipientEmail);

        $transport = $transportBuilder->getTransport();
        $transport->getMessage()->setSubject('Nistruct Contact Form Submission');

        $this->_writeLog('Template Vars After TransportBuilder: ' . print_r([
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'date' => $date
        ], true));

        $transport->sendMessage();

        $this->_writeLog('Email sent successfully.');

    } catch (\Exception $e) {
        $this->_writeLog('Email sending error: ' . $e->getMessage());
    }
}









    protected function _writeLog($message)
    {
        $this->logger->error($message);
    }
}