<?php

namespace Cyclr\Webhook\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;

		$installer->startSetup();

		if (version_compare($context->getVersion(), '0.7.0', '<')) {
			if (!$installer->tableExists('queued_hook_events')) {
				$table = $installer->getConnection()->newTable(
					$installer->getTable('queued_hook_events')
				)
					->addColumn(
						'event_id',
						\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
						null,
						[
							'identity' => true,
							'nullable' => false,
							'primary'  => true,
							'unsigned' => true,
						],
						'Event ID'
					)
					->addColumn(
						'hook_type',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						['nullable => false'],
						'Hook Type'
					)
					->addColumn(
						'updated_at',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						['nullable => false'],
						'Updated At'
					)
					->addColumn(
						'url',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						['nullable => false'],
						'Webhook URL'
					)
					->addColumn(
						'entity_id',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						255,
						['nullable => false'],
						'entity_id'
					)
					->addColumn(
						'body_json',
						\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
						('2M'),
						[],
						'Body JSON'
					)
					->setComment('Queued Hook Events');
				$installer->getConnection()->createTable($table);

				$installer->getConnection()->addIndex(
					$installer->getTable('queued_hook_events'),
					$setup->getIdxName(
						$installer->getTable('queued_hook_events'),
						['hook_type', 'body_json', 'url', 'updated_at', 'entity_id'],
						\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
					),
					['hook_type', 'body_json', 'url', 'updated_at', 'entity_id'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				);
			}
		}

		$installer->endSetup();
	}
}
