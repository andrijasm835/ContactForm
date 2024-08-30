<?php
namespace Nistruct\ContactForm\Model;

use Magento\Framework\Model\AbstractModel;

class Contact extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Nistruct\ContactForm\Model\ResourceModel\Contact');
    }
}

