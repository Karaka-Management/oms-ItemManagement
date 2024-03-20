<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\ItemManagement\Controller\ApiController;
use Modules\ItemManagement\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/item/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiController:apiItemListExport',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/item/find(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiController:apiItemFind',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/item/attribute$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/item/attribute/type$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeTypeCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeTypeUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/item/attribute/type/l11n$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeTypeL11nCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeTypeL11nUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/item/attribute/value$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeValueCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeValueUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/item/attribute/value$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeValueL11nCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiAttributeController:apiItemAttributeValueL11nUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/item/l11n$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiController:apiItemL11nCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiController:apiItemL11nUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/item/l11n/type$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiController:apiItemL11nTypeCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
        [
            'dest'       => '\Modules\ItemManagement\Controller\ApiController:apiItemL11nTypeUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
];
