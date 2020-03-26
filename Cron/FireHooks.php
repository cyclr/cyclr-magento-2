<?php

namespace Cyclr\Webhook\Cron;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\HTTP\Adapter\Curl;
use \Magento\Framework\Json\Helper\Data;
use \Cyclr\Webhook\Model\WebhookFactory;
use \Cyclr\Webhook\Model\HookEventFactory;

class FireHooks
{
  protected $logger;
  protected $curlAdapter;
  protected $jsonHelper;
  protected $webhookFactory;
  protected $hookEventFactory;

  public function __construct(
    LoggerInterface $logger,
    Curl $curlAdapter,
    Data $jsonHelper,
    WebhookFactory $webhookFactory,
    HookEventFactory $hookEventFactory
  ) {

    $this->logger = $logger;
    $this->curlAdapter = $curlAdapter;
    $this->jsonHelper = $jsonHelper;
    $this->webhookFactory = $webhookFactory;
    $this->hookEventFactory = $hookEventFactory;
  }

  public function execute()
  {
    $this->logger->info('Firing Hooks');

    $webhooks = $this->webhookFactory->create()->getCollection();
    $hookEvents = $this->hookEventFactory->create()->getCollection();

    foreach ($webhooks->getItems() as $webhook) {
      foreach ($hookEvents->getItems() as $hookEvent) {
        if ($hookEvent['hook_type'] == $webhook['event']  && $hookEvent['url'] == $webhook['url']) {
          $headers = ["Content-Type: application/json"];
          $this->curlAdapter->write('POST', $webhook['url'], '1.1', $headers, $hookEvent['body_json']);
          $this->curlAdapter->read();
          $this->curlAdapter->close();
        }
      }
    }
    $clearEvents = $hookEvents->walk('delete');
  }
}
