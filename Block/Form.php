<?php
namespace Nistruct\ContactForm\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\RequestInterface; // Dodano za rad sa zahtevima

class Form extends Template
{
    protected $scopeConfig;
    protected $request; // Dodano za rad sa zahtevima

    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request, // Dodano za rad sa zahtevima
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request; // Dodano za rad sa zahtevima
        parent::__construct($context, $data);
    }

    /**
     * Check if contact form is enabled
     *
     * @return bool
     */
    public function isContactFormEnabled()
    {
        return $this->scopeConfig->getValue(
            'nistruct_contactform/contact_form_settings/enable_form',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('nistruct-contact-form/index/post');
    }

    /**
     * Get form field value
     *
     * @param string $field
     * @return string
     */
    public function getFormFieldValue($field)
    {
        return $this->request->getParam($field, ''); // Koristite getParam() za pristup POST podacima
    }
}
