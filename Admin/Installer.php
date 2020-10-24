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

        $id                       = $itemAttrType['color']->getId();
        $itemAttrValue['color']   = [];
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Red', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][0]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Rot', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][1]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Blue', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][2]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Blau', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][3]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Green', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][4]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Grün', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][5]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Yellow', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][6]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Gelb', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][7]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'White', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][8]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Weiss', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][9]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Black', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][10]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Schwarz', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][11]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Braun', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][12]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Braun', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][13]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Purple', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][14]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Lila', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][15]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Pink', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][16]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Rosa', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][17]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Orange', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][18]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Orange', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][19]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Grey', ISO639x1Enum::_EN);
        ItemAttributeValueMapper::create($itemAttrValue['color'][20]);
        $itemAttrValue['color'][] = new ItemAttributeValue($id, 'Grau', ISO639x1Enum::_DE);
        ItemAttributeValueMapper::create($itemAttrValue['color'][21]);

        return $itemAttrValue;
    }
}
