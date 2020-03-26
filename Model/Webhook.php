<?php

namespace Cyclr\Webhook\Model;

/**
 * Webhook model
 *
 * @method \Cyclr\Webhook\Model\Resource\Webhook _getResource()
 * @method \Cyclr\Webhook\Model\Resource\Webhook getResource()
 * @method string getCode()
 * @method \Cyclr\Webhook\Model\Webhook setCode(string $value)
 * @method string getName()
 * @method \Cyclr\Webhook\Model\Webhook setName(string $value)
 */
class Webhook extends \Magento\Framework\Model\AbstractModel
{
    const TYPE_TEXT = 'text';

    const TYPE_HTML = 'html';

    /**
     * @var int
     */
    protected $_storeId = 0;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper = null;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Escaper $escaper
     * @param \Cyclr\Webhook\Model\Resource\Webhook $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Escaper $escaper,
        \Cyclr\Webhook\Model\Resource\Webhook $resource,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_escaper = $escaper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Internal Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Cyclr\Webhook\Model\Resource\Webhook');
    }

    /**
     * Setter
     *
     * @param integer $storeId
     * @return $this
     * @codeCoverageIgnore
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Getter
     *
     * @return integer
     * @codeCoverageIgnore
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Load webhook by code
     *
     * @param string $code
     * @return $this
     * @codeCoverageIgnore
     */
    public function loadByCode($code)
    {
        $this->getResource()->loadByCode($this, $code);
        return $this;
    }

    /**
     * Return webhook value depend on given type
     *
     * @param string $type
     * @return string
     */
    public function getValue($type = null)
    {
        if ($type === null) {
            $type = self::TYPE_HTML;
        }
        if ($type == self::TYPE_TEXT || !strlen((string)$this->getData('html_value'))) {
            $value = $this->getData('plain_value');
            //escape html if type is html, but html value is not defined
            if ($type == self::TYPE_HTML) {
                $value = nl2br($this->_escaper->escapeHtml($value));
            }
            return $value;
        }
        return $this->getData('html_value');
    }

    /**
     * Validation of object data
     *
     * @return \Magento\Framework\Phrase|bool
     */
    public function validate()
    {
        $url = $this->getData('url');
        if (!preg_match('/^https?:\/\/.+\/api\/webhook\/.{8}$/i', $url)) {
            return new \Magento\Framework\Phrase('Invalid URL. You should only use webhooks generated on Cyclr.');
        }

        return true;
    }

    /**
     * Retrieve webhooks option array
     * @todo: extract method as separate class
     * @param bool $withGroup
     * @return array
     */
    public function getWebhooksOptionArray($withGroup = false)
    {
        /* @var $collection \Cyclr\Webhook\Model\Resource\Webhook\Collection */
        $collection = $this->getCollection();
        $webhooks = [];
        foreach ($collection->toOptionArray() as $webhook) {
            $webhooks[] = [
                'value' => '{{customVar code=' . $webhook['value'] . '}}',
                'label' => __('%1', $webhook['label']),
            ];
        }
        if ($withGroup && $webhooks) {
            $webhooks = ['label' => __('Webhooks'), 'value' => $webhooks];
        }
        return $webhooks;
    }
}
