<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\ItemManagement\Controller\BackendController;
use Modules\ItemManagement\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/item/attribute/type/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementAttributeTypeList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^/item/attribute/type/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementAttributeType',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^/item/attribute/type/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementAttributeTypeCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^/item/attribute/value/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementAttributeValue',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^/item/attribute/value/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementAttributeValueCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^/item/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementItemList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^/item/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementItemCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^/item/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementItem',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],

    '^/sales/item/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementSalesList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^/sales/item/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementSalesItem',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],

    '^/purchase/item/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementPurchaseList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PURCHASE_ITEM,
            ],
        ],
    ],
    '^/purchase/item/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementPurchaseItem',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PURCHASE_ITEM,
            ],
        ],
    ],

    '^/warehouse/item/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementWarehousingList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::STOCK_ITEM,
            ],
        ],
    ],
    '^/warehouse/item/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementWarehouseItem',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::STOCK_ITEM,
            ],
        ],
    ],
    '^/purchase/analysis/item(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemPurchaseAnalysis',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PURCHASE_ITEM,
            ],
        ],
    ],

    '^/production/item/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementProductionList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^/production/item/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementProductionItem',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],

    '^/item/material/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemMaterialList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::MATERIAL,
            ],
        ],
    ],
    '^/item/material/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemMaterialView',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::MATERIAL,
            ],
        ],
    ],
    '^/item/material/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemMaterialCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::MATERIAL,
            ],
        ],
    ],
];
