<?php
namespace Nistruct\ContactForm\Model\ResourceModel\Contact;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Nistruct\ContactForm\Model\Contact', 'Nistruct\ContactForm\Model\ResourceModel\Contact');
    }
}
