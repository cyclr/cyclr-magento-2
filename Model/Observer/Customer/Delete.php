<?php

namespace Cyclr\Webhook\Model\Observer\Customer;

use Magento\Framework\Event\Observer;

/**
 * Class Customer
 */
class Delete extends \Cyclr\Webhook\Model\Observer\Customer\CustomerAbstract
{
    protected function _getWebhookEvent()
    {
        return 'customer/deleted';
    }
}
