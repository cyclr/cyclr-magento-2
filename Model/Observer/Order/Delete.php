<?php

namespace Cyclr\Webhook\Model\Observer\Order;

use Magento\Framework\Event\Observer;

/**
 * Class Order
 */
class Delete extends \Cyclr\Webhook\Model\Observer\Order\OrderAbstract
{
    protected function _getWebhookEvent()
    {
        return 'order/deleted';
    }
}
