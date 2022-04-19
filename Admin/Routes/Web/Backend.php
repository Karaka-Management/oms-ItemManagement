<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\ItemManagement\Controller\BackendController;
use Modules\ItemManagement\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/item/attribute/type.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementAttributeTypes',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^.*/item/attribute/value.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementAttributeValues',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ATTRIBUTE,
            ],
        ],
    ],
    '^.*/sales/item/list.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementSalesList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/purchase/item/list.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementPurchaseList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PURCHASE_ITEM,
            ],
        ],
    ],
    '^.*/warehouse/item/list.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementWarehousingList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::STOCK_ITEM,
            ],
        ],
    ],
    '^.*/sales/item/create.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementSalesCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/purchase/item/create.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementPurchaseCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::PURCHASE_ITEM,
            ],
        ],
    ],
    '.*/warehouse/item/create.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementWarehousingCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::STOCK_ITEM,
            ],
        ],
    ],
    '^.*/sales/item/profile.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementSalesItem',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/purchase/item/profile.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementPurchaseItem',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PURCHASE_ITEM,
            ],
        ],
    ],
    '^.*/warehouse/item/profile.*$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemManagementWarehouseItem',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::STOCK_ITEM,
            ],
        ],
    ],
    '^.*/sales/analysis/item(\?.*|$)$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemSalesAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::SALES_ITEM,
            ],
        ],
    ],
    '^.*/purchase/analysis/item(\?.*|$)$' => [
        [
            'dest'       => '\Modules\ItemManagement\Controller\BackendController:viewItemPurchaseAnalysis',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::PURCHASE_ITEM,
            ],
        ],
    ],
];
