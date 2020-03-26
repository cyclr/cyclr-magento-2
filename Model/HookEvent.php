<?php

namespace Cyclr\Webhook\Model;

class HookEvent extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'queued_hook_events';

	protected $_cacheTag = 'queued_hook_events';

	protected $_eventPrefix = 'queued_hook_events';

	protected function _construct()
	{
		$this->_init('Cyclr\Webhook\Model\ResourceModel\HookEvent');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}
