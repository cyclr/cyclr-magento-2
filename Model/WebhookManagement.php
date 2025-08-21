<?php

namespace Cyclr\Webhook\Model;

use \Cyclr\Webhook\Model\WebhookFactory;

/**
 * Class WebhookManagement
 *
 * @package Cyclr\Webhook\Model
 */
class WebhookManagement implements \Cyclr\Webhook\Api\WebhookManagementInterface
{
    /**
     * @var WebhookFactory
     */
    private $webhookFactory;

    public function __construct(
        WebhookFactory $webhookFactory
    ) {
        $this->webhookFactory = $webhookFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function postWebhook($event, $url, $fields)
    {
        // Variables taken from post request to make hook.  
        // NB a few are hard-coded (status,authentication,content_type & method)
        $data_from_api_request = [
            'event' => $event,
            'url' => $url,
            'fields' => $fields
        ];

        // Create a webhook
        $hook_from_api = $this->webhookFactory->create();

        // Add the data from the post request (essentially emulating a form fill)
        $hook_from_api->addData($data_from_api_request);

        // Save the webhook to the system.
        $hook_from_api->save();

        // Convert the id of the hook into an array, for Cyclr to interpret.
        $id_of_hook_from_api = ['id' => $hook_from_api["webhook_id"]];
        return $id_of_hook_from_api;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteWebhook($id)
    {
        // Create a collection comprised of only the given webhook 
        $hookFinder = $this->webhookFactory->create()->getCollection()
            ->addFieldToFilter('webhook_id', $id);

        // Delete the given webhook if it exists
        $deleteAttempt = $hookFinder->walk('delete');
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getWebhook($id)
    {
        $allHooks = $this->webhookFactory->create()->getCollection()
            ->addFieldToFilter('webhook_id', $id);

        $hook_array = [];

        foreach ($allHooks as $hook) {
            $hook_details = [
                'webhook_id' => $hook->getId(),
                'event' => $hook->getEvent(),
                'url' => $hook->getUrl(),
                'fields' => $hook->getFields()
            ];
            array_push($hook_array, $hook_details);
        }

        return $hook_array;
    }

    /**
     * {@inheritdoc}
     */
    public function listWebhooks()
    {
        $allHooks = $this->webhookFactory->create()->getCollection();

        $hook_array = [];

        foreach ($allHooks as $hook) {
            $hook_details = [
                'webhook_id' => $hook->getId(),
                'event' => $hook->getEvent(),
                'url' => $hook->getUrl(),
                'fields' => $hook->getFields()
            ];
            array_push($hook_array, $hook_details);
        }

        return $hook_array;
    }
}
