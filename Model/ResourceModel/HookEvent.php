<?php

namespace Cyclr\Webhook\Model\ResourceModel;

class HookEvent extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	) {
		parent::__construct($context);
	}

	protected function _construct()
	{
		$this->_init('queued_hook_events', 'event_id');
	}
}
