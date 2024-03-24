<?php declare(strict_types=1);

namespace AventuxB2BTools\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('core')]
class Migration1711294376CreateCustomerApprovedField extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1711294376;
    }

    public function update(Connection $connection): void
    {
        $customFieldSetId = Uuid::fromHexToBytes(Uuid::fromStringToHex('aventux_b2b_tools'));

        $connection->executeStatement('
            INSERT INTO `custom_field_set`
                (`id`, `name`, `config`, `active`, `app_id`, `position`, `global`, `created_at`, `updated_at`)
            VALUES
                (:id, :name, :config, :active, :appId, :position, :global, :createdAt, :updatedAt)
        ', [
            'id' => $customFieldSetId,
            'name' => 'aventux_b2b_tools',
            'config' => '{"label": {"en-GB": "B2B tools"}, "translated": false}',
            'active' => 1,
            'appId' => null,
            'position' => 1,
            'global' => 0,
            'createdAt' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            'updatedAt' => null,
        ]);

        $customFieldSetRelationId = Uuid::fromHexToBytes(Uuid::fromStringToHex('aventux_b2b_tools_relation'));
        $connection->executeStatement('
            INSERT INTO `custom_field_set_relation`
                (`id`, `set_id`, `entity_name`, `created_at`, `updated_at`)
            VALUES
                (:id, :set_id, :entity_name, :created_at, :updated_at)
        ', [
            'id' => $customFieldSetRelationId,
            'set_id' => $customFieldSetId,
            'entity_name' => 'customer',
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            'updated_at' => null,
        ]);

        $customFieldId = Uuid::fromHexToBytes(Uuid::fromStringToHex('aventux_b2b_tools_customfield_id'));
        $connection->executeStatement('
        INSERT INTO `custom_field`
            (`id`, `name`, `type`, `config`, `active`, `set_id`, `created_at`, `updated_at`, `allow_customer_write`, `allow_cart_expose`)
        VALUES
            (:id, :name, :type, :config, :active, :set_id, :created_at, :updated_at, :allow_customer_write, :allow_cart_expose)
        ', [
            'id' => $customFieldId,
            'name' => 'aventux_b2b_tools_customer_approved',
            'type' => 'bool',
            'config' => '{"type": "switch", "label": {"en-GB": "Approved to login"}, "helpText": {"en-GB": "Approved to login"}, "componentName": "sw-field", "customFieldType": "switch", "customFieldPosition": 1}',
            'active' => 1,
            'set_id' => $customFieldSetId,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            'updated_at' => null,
            'allow_customer_write' => 1,
            'allow_cart_expose' => 0,
        ]);
    }
}
