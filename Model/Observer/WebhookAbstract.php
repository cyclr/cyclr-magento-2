<?php

namespace Cyclr\Webhook\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Customer
 */
class WebhookAbstract implements ObserverInterface
{
    /**
     * Webhook event
     * @var [type]
     */
    protected $_webhookEvent;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * Curl Adapter
     */
    protected $_curlAdapter;

    /**
     * Json Helper
     * @var [type]
     */
    protected $_jsonHelper;

    /**
     * Webhook factory
     * @var [type]
     */
    protected $_webhookFactory;

    /**
     * Hook Event factory
     * @var [type]
     */
    protected $_hookEventFactory;

    /**
     * @param \Psr\Logger\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\HTTP\Adapter\Curl $curlAdapter,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Cyclr\Webhook\Model\WebhookFactory $webhookFactory,
        \Cyclr\Webhook\Model\HookEventFactory $hookEventFactory
    ) {
        $this->_logger = $logger;
        $this->_curlAdapter = $curlAdapter;
        $this->_jsonHelper = $jsonHelper;
        $this->_webhookFactory = $webhookFactory;
        $this->_hookEventFactory = $hookEventFactory;
    }

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $this->_webhookEvent = $this->_getWebhookEvent();
        $eventData = $this->_getWebhookData($observer);
        $this->_flipUpdateEvent($observer);

        $body = [
            'event' => $this->_webhookEvent,
            'data'  => $eventData
        ];

        $webhooks = $this->_webhookFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('event', $this->_webhookEvent);

        foreach ($webhooks as $webhook) {
            $posted_fields = explode(",", $webhook->getFields());
            $returned_data = current($eventData);

            $payload_body = [];

            foreach ($posted_fields as $field) {
                if (isset($returned_data[$field])) {
                    $payload_body[$field] = $returned_data[$field];
                }
            }

            // Pass the full original data along with the filtered payload
            $this->_sendWebhook($webhook->getUrl(), $payload_body, $returned_data);
        }
    }

    /**
     * Flip the "updated" event to "created".
     *
     * @param Observer $observer
     * @return void
     */
    protected function _flipUpdateEvent(Observer $observer)
    {
        $updatedEvent = preg_match('/updated$/', strtolower($this->_webhookEvent));

        // If the webhook is not an "updated" event, don't do anything.
        if (!$updatedEvent)
            return;

        $dataObject = $observer->getEvent()->getDataObject();
        $createdAt = $dataObject->getData('created_at');
        $updatedAt = $dataObject->getData('updated_at');
        $isObjectNew = $dataObject->isObjectNew() || (!empty($createdAt) && !empty($updatedAt) && $createdAt == $updatedAt &&
            !preg_match('/^product/', strtolower($this->_webhookEvent)));
        // Magento 2 Bug: Product update doesn't change updated_at.

        // If the data object is new, trigger the "created" event instead.
        if ($isObjectNew)
            $this->_webhookEvent = preg_replace('/updated$/', 'created', strtolower($this->_webhookEvent));
    }

    protected function _sendWebhook($url, $body, $originalData = null)
    {
        $this->_logger->debug("Sending webhook for event " . $this->_webhookEvent . " to " . $url);

        // Use original data for entity_id and updated_at if available, otherwise fallback to body
        $entityId = isset($originalData['entity_id']) ? $originalData['entity_id'] : (isset($body['entity_id']) ? $body['entity_id'] : null);
        $updatedAt = isset($originalData['updated_at']) ? $originalData['updated_at'] : (isset($body['updated_at']) ? $body['updated_at'] : null);

        // Only check for existing jobs if we have both entity_id and updated_at
        if ($entityId && $updatedAt) {
            $existingJobs = $this->_hookEventFactory
                ->create()
                ->getCollection()
                ->addFieldToFilter('entity_id', $entityId)
                ->addFieldToFilter('updated_at', $updatedAt);

            if ($existingJobs->getSize() > 0) {
                return;
            }
        }

        $bodyJson = $this->_jsonHelper->jsonEncode($body);

        $hookEvent = $this->_hookEventFactory->create();
        $hookEventData = [
            "hook_type" => $this->_webhookEvent,
            "body_json" => $bodyJson,
            "url" => $url
        ];

        // Only add entity_id and updated_at if they exist
        if ($entityId) {
            $hookEventData["entity_id"] = $entityId;
        }
        if ($updatedAt) {
            $hookEventData["updated_at"] = $updatedAt;
        }

        $hookEvent->addData($hookEventData);
        $saveHookEvent = $hookEvent->save();
    }

    protected function _getWebhookEvent()
    {
        // TODO: Throw here because this is an abstract function
        return false;
    }

    protected function _getWebhookData(Observer $observer)
    {
        // TODO: Throw here because this is an abstract function
        return false;
    }
}
