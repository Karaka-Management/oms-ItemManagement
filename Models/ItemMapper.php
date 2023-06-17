<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\ItemManagement\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Models;

use Modules\Editor\Models\EditorDocMapper;
use Modules\Media\Models\Media;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\MediaType;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\BaseStringL11nType;

/**
 * Item mapper class.
 *
 * @package Modules\ItemManagement\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Item
 * @extends DataMapperFactory<T>
 */
final class ItemMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'itemmgmt_item_id'            => ['name' => 'itemmgmt_item_id',            'type' => 'int',          'internal' => 'id'],
        'itemmgmt_item_no'            => ['name' => 'itemmgmt_item_no',            'type' => 'string',       'internal' => 'number', 'autocomplete' => true],
        'itemmgmt_item_status'        => ['name' => 'itemmgmt_item_status',        'type' => 'int',          'internal' => 'status'],
        'itemmgmt_item_info'          => ['name' => 'itemmgmt_item_info',          'type' => 'string',       'internal' => 'info'],
        'itemmgmt_item_salesprice'    => ['name' => 'itemmgmt_item_salesprice',    'type' => 'Serializable', 'internal' => 'salesPrice'],
        'itemmgmt_item_purchaseprice' => ['name' => 'itemmgmt_item_purchaseprice', 'type' => 'Serializable', 'internal' => 'purchasePrice'],
        'itemmgmt_item_parent'        => ['name' => 'itemmgmt_item_parent', 'type' => 'int', 'internal' => 'parent'],
        'itemmgmt_item_unit'          => ['name' => 'itemmgmt_item_unit', 'type' => 'int', 'internal' => 'unit'],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'itemmgmt_item';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'itemmgmt_item_id';

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'files' => [
            'mapper'   => MediaMapper::class,            /* mapper of the related object */
            'table'    => 'itemmgmt_item_media',         /* table of the related object, null if no relation table is used (many->1) */
            'external' => 'itemmgmt_item_media_media',
            'self'     => 'itemmgmt_item_media_item',
        ],
        'notes' => [
            'mapper'   => EditorDocMapper::class,       /* mapper of the related object */
            'table'    => 'itemmgmt_item_note',         /* table of the related object, null if no relation table is used (many->1) */
            'external' => 'itemmgmt_item_note_doc',
            'self'     => 'itemmgmt_item_note_item',
        ],
        'l11n' => [
            'mapper'   => ItemL11nMapper::class,
            'table'    => 'itemmgmt_item_l11n',
            'self'     => 'itemmgmt_item_l11n_item',
            'external' => null,
        ],
        'attributes' => [
            'mapper'   => ItemAttributeMapper::class,
            'table'    => 'itemmgmt_item_attr',
            'self'     => 'itemmgmt_item_attr_item',
            'external' => null,
        ],
    ];

    /**
     * Get the item list
     *
     * @param string $language Language
     *
     * @return array
     *
     * @todo: experimental (not 100% working)
     *
     * @since 1.0.0
     */
    public static function getItemList(string $language) : array
    {
        // items
        $query = <<<SQL
        select itemmgmt_item.itemmgmt_item_id,
            itemmgmt_item.itemmgmt_item_no,
            itemmgmt_item.itemmgmt_item_salesprice,
            media.media_id,
            media.media_file,
            media_type.media_type_id,
            media_type.media_type_name
        from itemmgmt_item
        left join itemmgmt_item_media on itemmgmt_item.itemmgmt_item_id = itemmgmt_item_media.itemmgmt_item_media_item
        left join media on itemmgmt_item_media.itemmgmt_item_media_media = media.media_id
        left join media_type_rel on media.media_id = media_type_rel.media_type_rel_src
        left join media_type on media_type_rel.media_type_rel_dst = media_type.media_type_id and media_type.media_type_name = 'item_profile_image'
        SQL;

        $itemsResult = self::$db->con->query($query)->fetchAll();
        $items       = [];

        foreach ($itemsResult as $res) {
            $media = null;
            if ($res['media_id'] !== null) {
                $mediaType       = new MediaType();
                $mediaType->id   = $res['media_type_id'];
                $mediaType->name = $res['media_type_name'];

                $media     = new Media();
                $media->id = $res['media_id'];
                $media->setPath($res['media_file']);
            }

            $item         = new Item();
            $item->id     = $res['itemmgmt_item_id'];
            $item->number = $res['itemmgmt_item_no'];
            $item->salesPrice->setInt($res['itemmgmt_item_salesprice']);

            if ($media !== null) {
                $item->files[$media->id] = $media;
            }

            $items[$item->id] = $item;
        }

        // l11ns
        $query = <<<SQL
        select itemmgmt_item.itemmgmt_item_id,
            itemmgmt_item_l11n.itemmgmt_item_l11n_id,
            itemmgmt_item_l11n.itemmgmt_item_l11n_lang,
            itemmgmt_item_l11n.itemmgmt_item_l11n_typeref,
            itemmgmt_item_l11n.itemmgmt_item_l11n_description,
            itemmgmt_item_l11n_type.itemmgmt_item_l11n_type_title
        from itemmgmt_item
        left join itemmgmt_item_l11n
            on itemmgmt_item.itemmgmt_item_id = itemmgmt_item_l11n.itemmgmt_item_l11n_item
        left join itemmgmt_item_l11n_type
            on itemmgmt_item_l11n.itemmgmt_item_l11n_typeref = itemmgmt_item_l11n_type.itemmgmt_item_l11n_type_id
        where
            itemmgmt_item_l11n_type.itemmgmt_item_l11n_type_title in ('name1', 'name2', 'name3')
            and itemmgmt_item_l11n.itemmgmt_item_l11n_lang = :lang
        SQL;

        $sth = self::$db->con->prepare($query);
        $sth->execute(['lang' => $language]);

        $l11nsResult = $sth->fetchAll();

        foreach ($l11nsResult as $res) {
            $l11nType        = new BaseStringL11nType();
            $l11nType->id    = $res['itemmgmt_item_l11n_typeref'];
            $l11nType->title = $res['itemmgmt_item_l11n_type_title'];

            $l11n          = new BaseStringL11n();
            $l11n->id      = $res['itemmgmt_item_l11n_id'];
            $l11n->ref     = $res['itemmgmt_item_id'];
            $l11n->type    = $l11nType;
            $l11n->content = $res['itemmgmt_item_l11n_description'];
            $l11n->setLanguage($res['itemmgmt_item_l11n_lang']);

            $items[$l11n->ref]->addL11n($l11n);
        }

        return $items;
    }
}
