<?php

namespace Cyclr\Webhook\Cron;

use \Cyclr\Webhook\Model\HookEventFactory;
use \Cyclr\Webhook\Model\WebhookFactory;
use \Psr\Log\LoggerInterface;

class FireHooks
{

    protected $logger;
    protected $webhookFactory;
    protected $hookEventFactory;

    public function __construct(
        LoggerInterface $logger,
        WebhookFactory $webhookFactory,
        HookEventFactory $hookEventFactory
    ) {
        $this->logger = $logger;
        $this->webhookFactory = $webhookFactory;
        $this->hookEventFactory = $hookEventFactory;
    }

    public function execute()
    {
        //$this->logger->info('Webhook Extension: Opening Webhook collection');
        $webhooks = $this->webhookFactory->create()->getCollection();

        //$this->logger->info('Webhook Extension: Opening Hook Event Collection');
        $hookEvents = $this->hookEventFactory->create()->getCollection();

        //$this->logger->info('Webhook Extension: Storing Hooks as a variable');
        $hooks = $webhooks->getItems();

        //$this->logger->info('Webhook Extension: Storing Events as a variable');
        $events = $hookEvents->getItems();

        //$this->logger->info('Webhook Extension: Making an array to store jobs');
        $jobs = array();

        $this->logger->info('Webhook Extension: Counting events to process...');
        foreach ($hooks as $iterator => $webhook) {
            // Filter out any webhooks with a blank URL
            if (strlen($webhook['url'] < 0)) {
                unset($array[$elementKey]);
                continue;
            }
            foreach ($events as $hookEvent) {
                if ($hookEvent['hook_type'] == $webhook['event'] && $hookEvent['url'] == $webhook['url']) {
                    array_push($jobs, (object) [
                        'webhook_url' => $webhook['url'],
                        'body' => $hookEvent['body_json'],
                    ]);
                }
            }
        }

        $job_count = count($jobs);
        if ($job_count == 0) {
            $this->logger->info('Webhook Extension: No matching events found.');
        }

        if ($job_count > 0) {
            //$this->logger->info('Webhook Extension: Generate an array to contain the curl handles');
            $curl_arr = array();

            //$this->logger->info('Webhook Extension: Generating Multi-Curl handle');
            $mh = curl_multi_init();
            $this->logger->info('Webhook Extension: Found ' . $job_count . ' matching events.  Processing...');

            // Store the first curl handle manually, to avoid it being lost
            $curl_arr[0] = curl_init($jobs[0]->webhook_url);
            curl_setopt($curl_arr[0], CURLOPT_URL, $jobs[0]->webhook_url);
            curl_setopt($curl_arr[0], CURLOPT_POSTFIELDS, $jobs[0]->body);
            curl_setopt($curl_arr[0], CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_multi_add_handle($mh, $curl_arr[0]);

            // Cycle through the rest of the curl handles, adding them to the asynchronous queue
            $i = 1;
            do {
                if ($i !== 0 && $job_count > 1) {
                    $curl_arr[$i] = curl_init($jobs[$i]->webhook_url);
                    curl_setopt($curl_arr[$i], CURLOPT_URL, $jobs[$i]->webhook_url);
                    curl_setopt($curl_arr[$i], CURLOPT_POSTFIELDS, $jobs[$i]->body);
                    curl_setopt($curl_arr[$i], CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_multi_add_handle($mh, $curl_arr[$i]);
                }
                $i++;
            } while ($i < $job_count);

            $this->logger->info('Webhook Extension: Posting to webhooks...');
            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);

            while ($active && $mrc == CURLM_OK) {
                if (curl_multi_select($mh) != -1) {
                    do {
                        $mrc = curl_multi_exec($mh, $active);
                    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
                }
            }

            $q = 0;
            $this->logger->info('Webhook Extension: Clearing completed tasks...');
            do {
                curl_multi_remove_handle($mh, $curl_arr[$q]);
                $q++;
            } while ($q < $job_count);
            $clearEvents = $hookEvents->walk('delete');
        }
    }
}
