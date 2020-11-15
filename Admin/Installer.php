<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\ItemManagement\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Admin;

use Modules\ItemManagement\Models\ItemAttributeType;
use Modules\ItemManagement\Models\ItemAttributeTypeL11n;
use Modules\ItemManagement\Models\ItemAttributeTypeL11nMapper;
use Modules\ItemManagement\Models\ItemAttributeTypeMapper;
use Modules\ItemManagement\Models\ItemAttributeValue;
use Modules\ItemManagement\Models\ItemAttributeValueMapper;
use phpOMS\Config\SettingsInterface;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;
use Modules\ItemManagement\Models\AttributeValueType;

/**
 * Installer class.
 *
 * @package Modules\ItemManagement\Admin
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * {@inheritdoc}
     */
    public static function install(DatabasePool $dbPool, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($dbPool, $info, $cfgHandler);

        $attrTypes = self::createItemAttributeTypes();
        self::createItemAttributeValues($attrTypes);
    }

    /**
     * Install default attribute types
     *
     * @return ItemAttributeType[]
     *
     * @since 1.0.0
     */
    private static function createItemAttributeTypes() : array
    {
        $itemAttrType = [];

        $itemAttrType['color'] = new ItemAttributeType('color');
        ItemAttributeTypeMapper::create($itemAttrType['color']);
        ItemAttributeTypeL11nMapper::create(new ItemAttributeTypeL11n($itemAttrType['color']->getId(), 'Color', ISO639x1Enum::_EN));
        ItemAttributeTypeL11nMapper::create(new ItemAttributeTypeL11n($itemAttrType['color']->getId(), 'Farbe', ISO639x1Enum::_DE));

        return $itemAttrType;
    }

    /**
     * Create default attribute values for types
     *
     * @param ItemAttributeType[] $itemAttrType Attribute types
     *
     * @return array<string, ItemAttributeValue[]>
     *
     * @since 1.0.0
     */
    private static function createItemAttributeValues(array $itemAttrType) : array
    {
        $itemAttrValue = [];

        $itemAttrValue['color']   = [];
        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Red', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][0]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Rot', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][1]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Blue', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][2]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Blau', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][3]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Green', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][4]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Grün', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][5]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Yellow', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][6]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Gelb', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][7]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'White', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][8]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Weiss', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][9]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Black', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][10]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Schwarz', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][11]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Braun', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][12]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Braun', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][13]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Purple', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][14]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Lila', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][15]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Pink', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][16]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Rosa', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][17]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Orange', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][18]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Orange', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][19]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Grey', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][20]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Grau', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create($itemAttrValue['color'][21]);
        ItemAttributeTypeMapper::createRelation('defaults', $itemAttrType['color']->getId(), $id);

        return $itemAttrValue;
    }
}
