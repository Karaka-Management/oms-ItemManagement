<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\Localization\BaseStringL11nType;

/**
 * ItemL11nType mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of BaseStringL11nType
 * @extends DataMapperFactory<T>
 */
final class ItemL11nTypeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_item_l11n_type_id'       => ['name' => 'itemmgmt_item_l11n_type_id',    'type' => 'int',    'internal' => 'id'],
        'itemmgmt_item_l11n_type_title'    => ['name' => 'itemmgmt_item_l11n_type_title', 'type' => 'string', 'internal' => 'title'],
        'itemmgmt_item_l11n_type_required' => ['name' => 'itemmgmt_item_l11n_type_required', 'type' => 'bool', 'internal' => 'isRequired'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_item_l11n_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'itemmgmt_item_l11n_type_id';

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = BaseStringL11nType::class;
}
