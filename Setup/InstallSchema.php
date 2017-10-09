<?php

namespace Pmclain\OneClickCheckout\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
            ->newTable($installer->getTable('default_payment'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ],
                'ID'
            )
            ->addColumn(
                'payment_token_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Payment Token ID'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Customer ID'
            )
            ->addIndex(
                $installer->getIdxName(
                    'default_payment',
                    [
                        'customer_id',
                        'payment_token_id',
                    ],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [
                    'customer_id',
                    'payment_token_id',
                ],
                [
                    'type' => AdapterInterface::INDEX_TYPE_UNIQUE,
                ]
            )
            ->setComment('Default Payment Methods');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
