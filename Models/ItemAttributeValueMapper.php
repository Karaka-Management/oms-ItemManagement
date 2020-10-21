<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\Localization\Defaults\CountryMapper;
use phpOMS\Localization\Defaults\LanguageMapper;

/**
 * Item mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 *
 * @todo Do I really want to create a relation to the language mapper? It's not really needed right?
 */
final class ItemAttributeValueMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'itemmgmt_attr_value_id'       => ['name' => 'itemmgmt_attr_value_id',       'type' => 'int',    'internal' => 'id'],
        'itemmgmt_attr_value_default'    => ['name' => 'itemmgmt_attr_value_default',    'type' => 'bool', 'internal' => 'isDefault'],
        'itemmgmt_attr_value_type'      => ['name' => 'itemmgmt_attr_value_type',      'type' => 'int',    'internal' => 'type'],
        'itemmgmt_attr_value_valueStr' => ['name' => 'itemmgmt_attr_value_valueStr', 'type' => 'string', 'internal' => 'valueStr'],
        'itemmgmt_attr_value_valueInt' => ['name' => 'itemmgmt_attr_value_valueInt', 'type' => 'int', 'internal' => 'valueInt'],
        'itemmgmt_attr_value_valueDec' => ['name' => 'itemmgmt_attr_value_valueDec', 'type' => 'float', 'internal' => 'valueDec'],
        'itemmgmt_attr_value_valueDat' => ['name' => 'itemmgmt_attr_value_valueDat', 'type' => 'DateTime', 'internal' => 'valueDat'],
        'itemmgmt_attr_value_lang' => ['name' => 'itemmgmt_attr_value_lang', 'type' => 'string', 'internal' => 'language'],
        'itemmgmt_attr_value_country' => ['name' => 'itemmgmt_attr_value_country', 'type' => 'string', 'internal' => 'country'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:string, self:string, by?:string, column?:string}>
     * @since 1.0.0
     */
    protected static array $ownsOne = [
        'language' => [
            'mapper'        => LanguageMapper::class,
            'external'          => 'itemmgmt_attr_value_lang',
            'by'            => 'code2',
            'column'        => 'code2',
            'conditional'   => true,
        ],
        'country' => [
            'mapper'        => CountryMapper::class,
            'external'          => 'itemmgmt_attr_value_country',
            'by'            => 'code2',
            'column'        => 'code2',
            'conditional'   => true,
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'itemmgmt_attr_value';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'itemmgmt_attr_value_id';
}
