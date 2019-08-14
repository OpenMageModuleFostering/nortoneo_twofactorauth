<?php
/**
 * @package   Nortoneo_TwoFactorAuth
 * @author    Lukasz Szczedzina <contact@nortoneo.com>
 * @website   http://nortoneo.com
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$connection = $installer->getConnection();
$tableName = $installer->getTable('nortoneo_twofactorauth/userSettings');
$isTableExists = $connection->isTableExists($tableName);
if (!$isTableExists) {
    $table = $connection
        ->newTable($tableName)
        ->addColumn('settings_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'SETTINGS ID')
        ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'USER ID')
        ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'IS ACTIVE')
        ->addColumn('method', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'METHOD')
        ->addColumn('trust_last_ip', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'TRUST LAST IP')
        ->addColumn('last_ip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15, array(
            'nullable' => true,
        ), 'LAST IP')
        ->addColumn('discrepancy', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'DISCREPANCY')
        ->addColumn('secret', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ), 'SECRET')
        ->addForeignKey($installer->getFkName('nortoneo_twofactorauth/userSettings', 'user_id', 'admin/user', 'user_id'),
            'user_id', $installer->getTable('admin/user'), 'user_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addIndex(
            $this->getIdxName($tableName, 'user_id', Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
            'user_id',
            array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
        );

    $connection->createTable($table);
}
