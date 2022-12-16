<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Admin;

use Modules\ItemManagement\Models\AttributeValueType;
use Modules\ItemManagement\Models\ItemAttributeType;
use Modules\ItemManagement\Models\ItemAttributeTypeL11n;
use Modules\ItemManagement\Models\ItemAttributeTypeL11nMapper;
use Modules\ItemManagement\Models\ItemAttributeTypeMapper;
use Modules\ItemManagement\Models\ItemAttributeValue;
use Modules\ItemManagement\Models\ItemAttributeValueMapper;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\Config\SettingsInterface;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;

/**
 * Installer class.
 *
 * @package Modules\ItemManagement\Admin
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * {@inheritdoc}
     */
    public static function install(ApplicationAbstract $app, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($app, $info, $cfgHandler);

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
        ItemAttributeTypeMapper::create()->execute($itemAttrType['color']);
        ItemAttributeTypeL11nMapper::create()->execute(new ItemAttributeTypeL11n($itemAttrType['color']->getId(), 'Color', ISO639x1Enum::_EN));
        ItemAttributeTypeL11nMapper::create()->execute(new ItemAttributeTypeL11n($itemAttrType['color']->getId(), 'Farbe', ISO639x1Enum::_DE));

        // weight
        // segment_level_1
        // segment_level_2
        // segment_level_3
        // segment_level_4
        // product_group
        //      consumable
        //      packaging
        //      service
        //      machine
        //      spare part
        //      transportation

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
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][0]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Rot', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][1]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Blue', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][2]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Blau', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][3]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Green', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][4]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Grün', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][5]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Yellow', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][6]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Gelb', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][7]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'White', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][8]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Weiss', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][9]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Black', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][10]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Schwarz', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][11]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Braun', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][12]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Braun', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][13]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Purple', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][14]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Lila', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][15]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Pink', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][16]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Rosa', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][17]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Orange', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][18]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Orange', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][19]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Grey', ISO639x1Enum::_EN);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][20]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        $itemAttrValue['color'][] = new ItemAttributeValue(AttributeValueType::_STRING, 'Grau', ISO639x1Enum::_DE);
        $id                       = ItemAttributeValueMapper::create()->execute($itemAttrValue['color'][21]);
        ItemAttributeTypeMapper::writer()->createRelationTable('defaults', [$id], $itemAttrType['color']->getId());

        return $itemAttrValue;
    }
}
