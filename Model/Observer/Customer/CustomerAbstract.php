<?php

namespace Cyclr\Webhook\Model\Observer\Customer;

use Magento\Framework\Event\Observer;

/**
 * Class CustomerAbstract
 */
class CustomerAbstract extends \Cyclr\Webhook\Model\Observer\WebhookAbstract
{
    /**
     * Subscriber repository
     * @var [type]
     */
    protected $_subscriber;

    /**
     * @param \Psr\Logger\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\HTTP\Adapter\Curl $curlAdapter,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Cyclr\Webhook\Model\WebhookFactory $webhookFactory,
        \Cyclr\Webhook\Model\HookEventFactory $hookEventFactory,
        \Magento\Newsletter\Model\Subscriber $subscriber
    ) {
        $this->_subscriber = $subscriber;

        parent::__construct(
            $logger,
            $curlAdapter,
            $jsonHelper,
            $webhookFactory,
            $hookEventFactory
        );
    }

    protected function _getWebhookData(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $subscriber = $this->_subscriber->loadByCustomerId($customer->getData('id'));

        $data = $customer->getData();
        $data["is_subscribed"] = $subscriber->isSubscribed();

        /**
         * Unset sensitive data from the response.
         */
        unset($data['rp_token']);
        unset($data['rp_token_created_at']);
        unset($data['password_hash']);
        unset($data['failures_num']);
        unset($data['first_failure']);
        unset($data['lock_expires']);

        return [
            'customer' => $data
        ];
    }
}
