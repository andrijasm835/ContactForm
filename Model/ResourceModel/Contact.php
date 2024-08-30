<?php
namespace Nistruct\ContactForm\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Contact extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('nistruct_contact_form', 'entity_id');
    }
}

