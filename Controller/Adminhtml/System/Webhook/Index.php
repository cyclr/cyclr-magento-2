<?php

namespace Cyclr\Webhook\Controller\Adminhtml\System\Webhook;

class Index extends \Cyclr\Webhook\Controller\Adminhtml\System\Webhook
{
    /**
     * Index Action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->createPage();
        $resultPage->getConfig()->getTitle()->prepend(__('Cyclr Webhooks'));
        return $resultPage;
    }
}
