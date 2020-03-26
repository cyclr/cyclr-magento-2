<?php

namespace Cyclr\Webhook\Model\ResourceModel\HookEvent;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'event_id';
	protected $_eventPrefix = 'queued_hook_event_collection';
	protected $_eventObject = 'queued_event_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Cyclr\Webhook\Model\HookEvent', 'Cyclr\Webhook\Model\ResourceModel\HookEvent');
	}
}
