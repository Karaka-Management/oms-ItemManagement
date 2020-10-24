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
final class ItemAttributeTypeL11nMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'itemmgmt_attr_type_l11n_id'        => ['name' => 'itemmgmt_attr_type_l11n_id',       'type' => 'int',    'internal' => 'id'],
        'itemmgmt_attr_type_l11n_title'     => ['name' => 'itemmgmt_attr_type_l11n_title',    'type' => 'string', 'internal' => 'title', 'autocomplete' => true],
        'itemmgmt_attr_type_l11n_type'      => ['name' => 'itemmgmt_attr_type_l11n_type',      'type' => 'int',    'internal' => 'type'],
        'itemmgmt_attr_type_l11n_lang'      => ['name' => 'itemmgmt_attr_type_l11n_lang', 'type' => 'string', 'internal' => 'language'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    protected static array $ownsOne = [
        'language' => [
            'mapper'            => LanguageMapper::class,
            'external'          => 'itemmgmt_attr_type_l11n_lang',
            'by'                => 'code2',
            'column'            => 'code2',
            'conditional'       => true,
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'itemmgmt_attr_type_l11n';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'itemmgmt_attr_type_l11n_id';
}
