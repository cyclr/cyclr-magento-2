<?php

namespace Cyclr\Webhook\Model\Observer\Subscriber;

use Magento\Framework\Event\Observer;

/**
 * Class SubscriberAbstract
 */
class SubscriberAbstract extends \Cyclr\Webhook\Model\Observer\WebhookAbstract
{
    /**
     * Customer repository
     * @var [type]
     */
    protected $_customerRepositoryInterface;

    /**
     * @param \Psr\Logger\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\HTTP\Adapter\Curl $curlAdapter,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Cyclr\Webhook\Model\WebhookFactory $webhookFactory,
        \Cyclr\Webhook\Model\HookEventFactory $hookEventFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;

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
        $subscriber = $observer->getEvent()->getDataObject();
        $customer = $this->_customerRepositoryInterface->getById($subscriber->getData('customer_id'));

        $data = $customer->__toArray();
        $data["is_subscribed"] = $subscriber->isSubscribed();

        return [
            'customer' => $data
        ];
    }
}
