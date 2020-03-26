<?php

namespace Cyclr\Webhook\Model\Observer\Subscriber;

use Magento\Framework\Event\Observer;

/**
 * Class Subscriber
 */
class Save extends \Cyclr\Webhook\Model\Observer\Subscriber\SubscriberAbstract
{
    protected function _getWebhookEvent()
    {
        return 'subscriber/saved';
    }
}
