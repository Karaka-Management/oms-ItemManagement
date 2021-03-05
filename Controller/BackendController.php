<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\ItemManagement
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\ItemManagement\Controller;

use Model\SettingsEnum;
use Modules\Admin\Models\LocalizationMapper;
use Modules\Billing\Models\SalesBillMapper;
use Modules\Billing\Models\BillTypeL11n;
use Modules\ItemManagement\Models\ItemAttributeMapper;
use Modules\ItemManagement\Models\ItemL11nMapper;
use Modules\ItemManagement\Models\ItemMapper;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Localization\Money;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Stdlib\Base\SmartDateTime;
use phpOMS\Views\View;

/**
 * ItemManagement controller class.
 *
 * @package Modules\ItemManagement
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response));

        $items = ItemMapper::withConditional('language', $response->getLanguage())::getAfterPivot(0, null, 25);
        $view->addData('items', $items);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/purchase-item-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));

        $items = ItemMapper::withConditional('language', $response->getLanguage())::getAfterPivot(0, null, 25);
        $view->addData('items', $items);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehousingList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/stock-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004807001, $request, $response));

        $items = ItemMapper::withConditional('language', $response->getLanguage())::getAfterPivot(0, null, 25);
        $view->addData('items', $items);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehousingCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/item-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004807001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementSalesItem(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $head = $response->get('Content')->getData('head');
        $head->addAsset(AssetType::CSS, 'Resources/chartjs/Chartjs/chart.css');
        $head->addAsset(AssetType::JSLATE, 'Resources/chartjs/Chartjs/chart.js');
        $head->addAsset(AssetType::JSLATE, 'Modules/ItemManagement/Controller.js', ['type' => 'module']);

        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-profile');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004805001, $request, $response));

        $item = ItemMapper::withConditional('language', $response->getLanguage())::get((int) $request->getData('id'));
        $view->addData('item', $item);

        $settings = $this->app->appSettings->get(null, [
            SettingsEnum::DEFAULT_LOCALIZATION,
        ]);

        $view->setData('defaultlocalization', LocalizationMapper::get((int) $settings['id']));

        $itemL11n = ItemL11nMapper::withConditional('language', $response->getLanguage())
            ::withConditional('item', $item->getId())::getAll();
        $view->addData('itemL11n', $itemL11n);

        $itemAttribute = ItemAttributeMapper::withConditional('language', $response->getLanguage())
            ::withConditional('item', $item->getId())::getAll();
        $view->addData('itemAttribute', $itemAttribute);

        // stats
        if ($this->app->moduleManager->isActive('Billing')) {
            $ytd       = SalesBillMapper::getSalesByItemId($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $mtd       = SalesBillMapper::getSalesByItemId($item->getId(), new SmartDateTime('Y-m-01'), new SmartDateTime('now'));
            $avg       = SalesBillMapper::getAvgSalesPriceByItemId($item->getId(), (new SmartDateTime('now'))->smartModify(-1), new SmartDateTime('now'));
            $lastOrder = SalesBillMapper::getLastOrderDateByItemId($item->getId());
            // @todo: why is the conditional array necessary, shouldn't the mapper realize when it mustn't use the conditional (when the field doesn't exist in the mapper)
            $newestInvoices    = SalesBillMapper::withConditional('language', $response->getLanguage(), [BillTypeL11n::class])::getNewestItemInvoices($item->getId(), 5);
            $topCustomers      = SalesBillMapper::getItemTopCustomers($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'), 5);
            $regionSales       = SalesBillMapper::getItemRegionSales($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'));
            $countrySales      = SalesBillMapper::getItemCountrySales($item->getId(), new SmartDateTime('Y-01-01'), new SmartDateTime('now'), 5);
            $monthlySalesCosts = SalesBillMapper::getItemMonthlySalesCosts($item->getId(), (new SmartDateTime('now'))->createModify(-1), new SmartDateTime('now'));
        } else {
            $ytd               = new Money();
            $mtd               = new Money();
            $avg               = new Money();
            $lastOrder         = null;
            $newestInvoices    = [];
            $topCustomers      = [];
            $regionSales       = [];
            $countrySales      = [];
            $monthlySalesCosts = [];
        }

        $view->addData('ytd', $ytd);
        $view->addData('mtd', $mtd);
        $view->addData('avg', $avg);
        $view->addData('lastOrder', $lastOrder);
        $view->addData('newestInvoices', $newestInvoices);
        $view->addData('topCustomers', $topCustomers);
        $view->addData('regionSales', $regionSales);
        $view->addData('countrySales', $countrySales);
        $view->addData('monthlySalesCosts', $monthlySalesCosts);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementPurchaseItem(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = $this->viewItemManagementSalesItem($request, $response, $data);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-profile');
        $view->setData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));


        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewItemManagementWarehouseItem(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = $this->viewItemManagementSalesItem($request, $response, $data);
        $view->setTemplate('/Modules/ItemManagement/Theme/Backend/sales-item-profile');
        $view->setData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004806001, $request, $response));


        return $view;
    }
}
