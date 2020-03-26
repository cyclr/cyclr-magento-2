<?php

namespace Cyclr\Webhook\Api;

/**
 * Interface WebhookManagementInterface
 *
 * @package Cyclr\Webhook\Api
 */
interface WebhookManagementInterface
{

    /**
     * POST for Webhook api
     * @param string $event
     * @param string $url
     * @param string $fields
     * @return string
     */
    public function postWebhook($event, $url, $fields);

    /**
     * DELETE for Webhook api
     * @param string $id
     * @return string
     */
    public function deleteWebhook($id);

    /**
     * GET for Webhook api
     * @param string $id
     * @return string
     */
    public function getWebhook($id);

    /**
     * GET for Webhook api
     * @return string
     */
    public function listWebhooks();
}
