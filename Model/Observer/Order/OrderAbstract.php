<?php

namespace Cyclr\Webhook\Model\Observer\Order;

use Magento\Framework\Event\Observer;

/**
 * Class OrderAbstract
 */
class OrderAbstract extends \Cyclr\Webhook\Model\Observer\WebhookAbstract
{
    /**
     * Order repository
     * @var [type]
     */
    protected $_order;

    /**
     * @param \Psr\Logger\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\HTTP\Adapter\Curl $curlAdapter,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Cyclr\Webhook\Model\WebhookFactory $webhookFactory,
        \Cyclr\Webhook\Model\HookEventFactory $hookEventFactory,
        \Magento\Sales\Model\Order $order
    ) {
        $this->_order = $order;

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
        $order = $observer->getEvent()->getOrder();
        $orderData = $this->_order->loadByIncrementId($order->getData('increment_id'));

        $data = $orderData->getData();
        $data["items"] = [];

        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $categoryIds = [];
            if ($product)
                $categoryIds = $product->getCategoryIds();

            $itemData = $item->getData();
            $itemData["category_ids"] = $categoryIds;
            array_push($data["items"], $itemData);
        }

        return [
            'order' => $data
        ];
    }
}
