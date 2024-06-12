<?php declare(strict_types=1);

namespace AventuxB2BTools\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1718123338CreateCustomerCHECodeField extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1718123338;
    }

    public function update(Connection $connection): void
    {
        $customFieldSetId = Uuid::fromHexToBytes(Uuid::fromStringToHex('aventux_b2b_tools'));
        $customFieldId = Uuid::fromHexToBytes(Uuid::fromStringToHex('aventux_b2b_tools_customfield_che_code_id'));

        $connection->executeStatement('
        INSERT INTO `custom_field`
            (`id`, `name`, `type`, `config`, `active`, `set_id`, `created_at`, `updated_at`, `allow_customer_write`, `allow_cart_expose`)
        VALUES
            (:id, :name, :type, :config, :active, :set_id, :created_at, :updated_at, :allow_customer_write, :allow_cart_expose)
        ', [
            'id' => $customFieldId,
            'name' => 'aventux_b2b_tools_customer_che_code',
            'type' => 'text',
            'config' => '{"type": "text", "label": {"en-GB": "Commercial register code"}, "helpText": {"en-GB": "Commercial register code"}, "componentName": "sw-field", "customFieldType": "text", "customFieldPosition": 2}',
            'active' => 1,
            'set_id' => $customFieldSetId,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            'updated_at' => null,
            'allow_customer_write' => 1,
            'allow_cart_expose' => 0,
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
